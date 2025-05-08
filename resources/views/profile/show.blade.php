@extends('layouts.app')

@section('content')
<div class="container py-6">
    <div class="feature-card">
        <div class="text-center mb-6">
            <h2 class="section-title">Perfil de Usuario</h2>
        </div>

        <div class="p-6">
            <div class="feature-card">
                <h3 class="h4 mb-4 text-center">Información Personal</h3>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="block text-sm font-medium mb-2">Nombre Completo</label>
                        <p class="text-muted">{{ $user->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="block text-sm font-medium mb-2">Correo Electrónico</label>
                        <p class="text-muted">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-top text-center">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-2"></i>{{ __('Editar Perfil') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection