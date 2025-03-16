<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-800">Detalles del Estudiante</h1>
                        <div class="space-x-2">
                            <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:border-purple-800 focus:ring focus:ring-purple-200 active:bg-purple-800 disabled:opacity-25 transition">
                                Editar
                            </a>
                            <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 disabled:opacity-25 transition">
                                Volver a Lista
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h5 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Nombre</h5>
                            <p class="text-lg text-gray-900">{{ $student->name }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h5 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Grupo</h5>
                            <p class="text-lg text-gray-900">{{ $student->group }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h5 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Matrícula</h5>
                            <p class="text-lg text-gray-900">{{ $student->matricula }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h5 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Teléfono</h5>
                            <p class="text-lg text-gray-900">{{ $student->tel }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h5 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Correo Electrónico</h5>
                            <p class="text-lg text-gray-900">{{ $student->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>