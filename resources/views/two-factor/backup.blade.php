<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Ingresa uno de tus códigos de respaldo para acceder a tu cuenta.
        Cada código solo puede usarse una vez.
    </div>

    <form method="POST" action="{{ route('two-factor.backup.post') }}">
        @csrf
        <div class="mb-4">
            <x-input-label for="backup_code" value="Código de respaldo" />
            <x-text-input
                id="backup_code" name="backup_code" type="text"
                autocomplete="off"
                class="block mt-1 w-full text-center tracking-widest text-lg uppercase"
                placeholder="XXXX-XXXX" autofocus />
            <x-input-error :messages="$errors->get('backup_code')" class="mt-2" />
        </div>
        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('two-factor.verify') }}"
                class="text-sm text-gray-500 hover:underline">
                Volver al código OTP
            </a>
            <x-primary-button>Usar código de respaldo</x-primary-button>
        </div>
    </form>
</x-guest-layout>