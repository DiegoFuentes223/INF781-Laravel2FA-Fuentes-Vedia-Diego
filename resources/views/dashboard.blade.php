<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
                <div class="p-6 border-t border-gray-200">
                    @if(auth()->user()->two_factor_enabled)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 mr-3">
                            2FA Activado
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 mr-3">
                            2FA Desactivado
                        </span>
                    @endif
                    <a href="{{ route('two-factor.setup') }}" class="text-blue-600 hover:underline text-sm">
                        Configurar Autenticación en Dos Factores
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>