<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TwoFactorBackupController extends Controller
{
    // Muestra el formulario de código de respaldo
    public function show()
    {
        return view('two-factor.backup');
    }

    // Verifica el código de respaldo
    public function verify(Request $request)
    {
        $request->validate(['backup_code' => 'required|string']);

        $user = $request->user();
        $inputCode = strtoupper(trim($request->backup_code));

        // Buscar entre los códigos no usados
        $backupCode = $user->twoFactorBackupCodes()
            ->where('used', false)
            ->get()
            ->first(fn($bc) => Hash::check($inputCode, $bc->code));

        if (!$backupCode) {
            return back()->withErrors([
                'backup_code' => 'Código de respaldo inválido o ya utilizado.'
            ]);
        }

        // Marcar como usado
        $backupCode->update(['used' => true]);

        // Marcar sesión como verificada
        $request->session()->put('two_factor_verified', true);

        return redirect()->intended(route('dashboard'));
    }
}