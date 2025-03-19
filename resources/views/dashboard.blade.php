<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--primary-text)] leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[var(--primary-bg)] overflow-hidden shadow-sm sm:rounded-lg border border-[var(--button-border)]">
                <div class="p-6 text-[var(--primary-text)]">
                    {{ __('You\'re logged in!') }}
                </div>
                <div class="p-4 space-x-4">
                    <a href="{{ route('files.index') }}" class="inline-flex items-center px-4 py-2 bg-[var(--button-bg)] border border-[var(--button-border)] rounded-md font-semibold text-xs text-[var(--button-text)] uppercase tracking-widest hover:bg-[var(--button-hover-bg)] hover:text-[var(--button-hover-text)] focus:bg-[var(--button-hover-bg)] focus:text-[var(--button-hover-text)] active:bg-[var(--button-hover-bg)] active:text-[var(--button-hover-text)] focus:outline-none focus:ring-2 focus:ring-[var(--accent-color)] focus:ring-offset-2 transition ease-in-out duration-150">Files</a>
                    @role('tutor')
                    <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 bg-[var(--button-bg)] border border-[var(--button-border)] rounded-md font-semibold text-xs text-[var(--button-text)] uppercase tracking-widest hover:bg-[var(--button-hover-bg)] hover:text-[var(--button-hover-text)] focus:bg-[var(--button-hover-bg)] focus:text-[var(--button-hover-text)] active:bg-[var(--button-hover-bg)] active:text-[var(--button-hover-text)] focus:outline-none focus:ring-2 focus:ring-[var(--accent-color)] focus:ring-offset-2 transition ease-in-out duration-150">Students</a>
                    @endrole
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
