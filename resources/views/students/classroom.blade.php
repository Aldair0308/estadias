<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Aula Virtual') }}
        </h2>
    </x-slot>
<div class="container">
    <div class="page-header">
        <h1>{{ __('Aula Virtual') }}</h1>
    </div>

    <div class="row">
        <!-- Panel de Cursos -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Mis Cursos') }}</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action active">
                            Matemáticas Avanzadas
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            Programación Web
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            Bases de Datos
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Principal -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Actividades Recientes') }}</h5>
                    <button class="btn btn-sm btn-primary">{{ __('Nueva Tarea') }}</button>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Tarea: Examen Parcial</h6>
                                <small class="text-muted">3 days ago</small>
                            </div>
                            <p class="mb-1">Fecha de entrega: 15/05/2023</p>
                            <small class="text-success">Entregado</small>
                        </div>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Foro: Preguntas sobre SQL</h6>
                                <small class="text-muted">1 week ago</small>
                            </div>
                            <p class="mb-1">Participa en la discusión</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendario -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Calendario') }}</h5>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>

<style>
    .page-header {
        background: linear-gradient(135deg, var(--header-gradient-start), var(--header-gradient-end));
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .card {
        border: none;
        background-color: var(--card-bg);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .list-group-item {
        background-color: var(--card-bg);
        border-color: var(--border-color);
        color: var(--text-color);
    }
    
    #calendar {
        min-height: 300px;
    }
</style>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar calendario aquí
    });
</script>
@endsection

@endsection