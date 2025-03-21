<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agregar Estudiante') }}
        </h2>
    </x-slot>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Agregar Estudiante</h1>
            <a href="{{ route('students.index') }}" class="px-4 py-2 bg-[var(--button-bg)] text-[var(--button-text)] border border-[var(--button-border)] rounded-md hover:bg-[var(--button-hover-bg)] hover:text-[var(--button-hover-text)] transition-colors">
                Volver a Lista
            </a>
        </div>

        <div class="bg-[var(--primary-bg)] overflow-hidden shadow-xl sm:rounded-lg border border-[var(--button-border)]">
            <div class="p-6 bg-[var(--primary-bg)] border-b border-[var(--button-border)]">
                <form action="{{ route('students.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" class="mt-1 block w-full rounded-md border-[var(--button-border)] bg-[var(--primary-bg)] text-[var(--primary-text)] shadow-sm focus:border-[var(--accent-color)] focus:ring-[var(--accent-color)] @error('name') border-red-500 @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="group" class="block text-sm font-medium text-gray-700">Grupo</label>
                        <input type="text" class="mt-1 block w-full rounded-md border-[var(--button-border)] bg-[var(--primary-bg)] text-[var(--primary-text)] shadow-sm focus:border-[var(--accent-color)] focus:ring-[var(--accent-color)] @error('group') border-red-500 @enderror" id="group" name="group" value="{{ old('group') }}" required>
                        @error('group')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="matricula" class="block text-sm font-medium text-gray-700">Matrícula</label>
                        <input type="number" class="mt-1 block w-full rounded-md border-[var(--button-border)] bg-[var(--primary-bg)] text-[var(--primary-text)] shadow-sm focus:border-[var(--accent-color)] focus:ring-[var(--accent-color)] @error('matricula') border-red-500 @enderror" id="matricula" name="matricula" value="{{ old('matricula') }}" required>
                        @error('matricula')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="tel" class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="tel" class="mt-1 block w-full rounded-md border-[var(--button-border)] bg-[var(--primary-bg)] text-[var(--primary-text)] shadow-sm focus:border-[var(--accent-color)] focus:ring-[var(--accent-color)] @error('tel') border-red-500 @enderror" id="tel" name="tel" value="{{ old('tel') }}" required>
                        @error('tel')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" class="mt-1 block w-full rounded-md border-[var(--button-border)] bg-[var(--primary-bg)] text-[var(--primary-text)] shadow-sm focus:border-[var(--accent-color)] focus:ring-[var(--accent-color)] @error('email') border-red-500 @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="px-4 py-2 bg-[var(--accent-color)] text-[var(--button-hover-text)] rounded-md hover:bg-[var(--button-hover-bg)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--accent-color)]">Guardar Estudiante</button>
                    </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>