<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorVerifyController extends Controller
{
    public function show()
    {
        return view('two-factor.verify');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $user = $request->user();
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Código OTP inválido. Intenta de nuevo.']);
        }

        $request->session()->put('two_factor_verified', true);

        return redirect()->intended(route('dashboard'));
    }
}