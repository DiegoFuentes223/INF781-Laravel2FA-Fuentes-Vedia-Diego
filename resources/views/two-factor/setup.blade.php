<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Autenticación en Dos Factores (2FA)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                        {{ session('status') }}
                    </div>
                @endif

                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-400 rounded">
                        <p class="font-bold text-yellow-800 mb-2">
                            Guarda estos códigos de respaldo en un lugar seguro.
                            No volverán a mostrarse.
                        </p>
                        <div class="grid grid-cols-2 gap-2 mt-3">
                            @foreach ($plainBackupCodes as $bc)
                                <code class="bg-white border border-gray-300 rounded px-3 py-1 text-sm tracking-widest text-center">
                                    {{ $bc }}
                                </code>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($enabled)
                    <div class="mb-6 p-4 bg-green-50 border border-green-300 rounded">
                        <p class="text-green-700 font-semibold">2FA está ACTIVADO en tu cuenta.</p>
                        @if ($hasBackupCodes)
                            <p class="text-sm text-gray-500 mt-1">Tienes códigos de respaldo disponibles.</p>
                        @else
                            <p class="text-sm text-red-500 mt-1">No tienes códigos de respaldo. Desactiva y reactiva el 2FA para generarlos.</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('two-factor.disable') }}">
                        @csrf
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                            onclick="return confirm('¿Deseas desactivar el 2FA?')">
                            Desactivar 2FA
                        </button>
                    </form>
                @else
                    <p class="mb-4 text-gray-700">
                        Escanea este código QR con tu app autenticadora y luego ingresa
                        el código de 6 dígitos para activar 2FA.
                    </p>
                    <div class="flex justify-center mb-4">
                        {!! $qrCodeSvg !!}
                    </div>
                    <div class="mb-6">
                        <p class="text-sm text-gray-500">O ingresa manualmente esta clave secreta:</p>
                        <code class="block bg-gray-100 p-2 rounded text-sm mt-1 tracking-widest">
                            {{ $secret }}
                        </code>
                    </div>
                    <form method="POST" action="{{ route('two-factor.enable') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Código OTP de verificación
                            </label>
                            <input type="text" name="code" maxlength="6"
                                autocomplete="one-time-code"
                                class="border border-gray-300 rounded px-3 py-2 w-40 text-center tracking-widest text-lg
                                       @error('code') border-red-500 @enderror"
                                placeholder="000000">
                            @error('code')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Activar 2FA
                        </button>
                    </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>