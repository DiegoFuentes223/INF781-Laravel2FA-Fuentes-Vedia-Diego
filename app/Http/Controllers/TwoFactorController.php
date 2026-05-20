<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use App\Models\TwoFactorBackupCode;

class TwoFactorController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $google2fa = new Google2FA();

        if (!$user->two_factor_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->update(['two_factor_secret' => $secret]);
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->two_factor_secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        // Recuperar backup codes en texto plano solo si recién se generaron
        $plainBackupCodes = session()->pull('backup_codes_plain', []);

        return view('two-factor.setup', [
            'qrCodeSvg'        => $qrCodeSvg,
            'secret'           => $user->two_factor_secret,
            'enabled'          => $user->two_factor_enabled,
            'plainBackupCodes' => $plainBackupCodes,
            'hasBackupCodes'   => $user->twoFactorBackupCodes()->where('used', false)->count() > 0,
        ]);
    }

    public function enable(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $user = $request->user();
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'El código OTP es inválido.']);
        }

        $user->update(['two_factor_enabled' => true]);

        // Eliminar códigos anteriores si existían
        $user->twoFactorBackupCodes()->delete();

        // Generar 8 backup codes
        $plainCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $plain = strtoupper(Str::random(4) . '-' . Str::random(4));
            $plainCodes[] = $plain;

            TwoFactorBackupCode::create([
                'user_id' => $user->id,
                'code'    => Hash::make($plain),
                'used'    => false,
            ]);
        }

        // Guardar en sesión para mostrarlos UNA SOLA VEZ
        session(['backup_codes_plain' => $plainCodes]);

        return redirect()->route('two-factor.setup')
            ->with('status', '2FA activado correctamente. Guarda tus códigos de respaldo.');
    }

    public function disable(Request $request)
    {
        $user = $request->user();
        $user->twoFactorBackupCodes()->delete();
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret'  => null,
        ]);

        return redirect()->route('two-factor.setup')
            ->with('status', '2FA desactivado.');
    }
}