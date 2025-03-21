<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--primary-text)] leading-tight">
            {{ __('Students') }}
        </h2>
    </x-slot>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-[var(--primary-text)]">Estudiantes</h1>
            <a href="{{ route('students.create') }}" class="px-4 py-2 bg-[var(--accent-color)] text-[var(--button-hover-text)] rounded-md hover:bg-[var(--button-hover-bg)] transition ease-in-out duration-150">
                Agregar Estudiante
            </a>
        </div>

    @if(session('success'))
        <div class="bg-[var(--button-bg)] border border-[var(--button-border)] text-[var(--button-text)] px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <span class="sr-only">Close</span>
                <svg class="h-6 w-6 text-[var(--accent-color)]" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <div class="bg-[var(--primary-bg)] overflow-hidden shadow-xl sm:rounded-lg border border-[var(--button-border)]">
        <div class="p-6 bg-[var(--primary-bg)] border-b border-[var(--button-border)]">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[var(--button-bg)]">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--button-text)] uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--button-text)] uppercase tracking-wider">Grupo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--button-text)] uppercase tracking-wider">Matrícula</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--button-text)] uppercase tracking-wider">Teléfono</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--button-text)] uppercase tracking-wider">Correo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--button-text)] uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr class="bg-[var(--primary-bg)] even:bg-[var(--button-bg)]">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--primary-text)]">{{ $student->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--primary-text)]">{{ $student->group }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--primary-text)]">{{ $student->matricula }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--primary-text)]">{{ $student->tel }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--primary-text)]">{{ $student->email }}</td>
                                <td>
                                    <div class="flex space-x-2" role="group">
                                        <a href="{{ route('students.show', $student) }}" class="inline-flex items-center px-3 py-1 border border-[var(--button-border)] text-sm leading-4 font-medium rounded-md text-[var(--button-hover-text)] bg-[var(--accent-color)] hover:bg-[var(--button-hover-bg)] focus:outline-none focus:ring-2 focus:ring-[var(--accent-color)] transition ease-in-out duration-150">
                                            Ver
                                        </a>
                                        <div class="inline-flex items-center px-3 py-1 border border-[var(--button-border)] text-sm leading-4 font-medium rounded-md text-[var(--button-hover-text)] bg-[var(--accent-color)] hover:bg-[var(--button-hover-bg)] focus:outline-none focus:ring-2 focus:ring-[var(--accent-color)] transition ease-in-out duration-150">
                                            <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center px-3 py-1 border border-[var(--button-border)] text-sm leading-4 font-medium rounded-md text-[var(--button-hover-text)] bg-[var(--accent-color)] hover:bg-[var(--button-hover-bg)] focus:outline-none focus:ring-2 focus:ring-[var(--accent-color)] transition ease-in-out duration-150">
                                                Editar
                                            </a>
                                        </div>
                                        <form action="{{ route('students.destroy', $student) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1 border border-[var(--button-border)] text-sm leading-4 font-medium rounded-md text-[var(--button-hover-text)] bg-[var(--accent-color)] hover:bg-[var(--button-hover-bg)] focus:outline-none focus:ring-2 focus:ring-[var(--accent-color)] transition ease-in-out duration-150" onclick="return confirm('¿Estás seguro de que deseas eliminar este estudiante?')">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-[var(--button-text)] text-center">
                                    No hay estudiantes registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>
</x-app-layout>