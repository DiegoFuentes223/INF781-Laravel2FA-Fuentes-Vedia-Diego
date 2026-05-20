<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Tu cuenta tiene activada la autenticación en dos factores.
        Ingresa el código de 6 dígitos de tu app autenticadora para continuar.
    </div>

    <form method="POST" action="{{ route('two-factor.verify.post') }}">
        @csrf
        <div class="mb-4">
            <x-input-label for="code" value="Código OTP" />
            <x-text-input
                id="code" name="code" type="text"
                maxlength="6" autocomplete="one-time-code"
                class="block mt-1 w-full text-center tracking-widest text-lg"
                placeholder="000000" autofocus />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>
        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('two-factor.backup') }}"
                class="text-sm text-gray-500 hover:underline">
                ¿Perdiste tu dispositivo? Usar código de respaldo
            </a>
            <x-primary-button>Verificar</x-primary-button>
        </div>
    </form>
</x-guest-layout>