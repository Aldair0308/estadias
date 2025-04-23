@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-[var(--primary-bg)] shadow rounded-lg overflow-hidden transition-colors duration-200">
        <div class="px-6 py-4 border-b border-[var(--button-border)]">
            <h2 class="text-xl font-semibold text-[var(--primary-text)]">Perfil de Usuario</h2>
        </div>

        <div class="p-6">
            <div class="bg-[var(--button-bg)] p-6 rounded-lg shadow-sm border border-[var(--button-border)]">
                <h3 class="text-lg font-medium mb-4 text-[var(--primary-text)]">Información Personal</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-[var(--primary-text)]">Nombre Completo</label>
                        <p class="mt-1 text-sm text-[var(--primary-text)]">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[var(--primary-text)]">Correo Electrónico</label>
                        <p class="mt-1 text-sm text-[var(--primary-text)]">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-[var(--button-border)]">
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-[var(--accent-color)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--accent-color)] focus:bg-[var(--accent-color)] active:bg-[var(--accent-color)] focus:outline-none focus:ring-2 focus:ring-[var(--accent-color)] focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Editar Perfil') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection