@if (request()->routeIs('filament.admin.auth.login'))
    {{-- Logo untuk halaman login --}}
    <div class="flex flex-col items-center">
        <img src="{{ asset('logo_upn.png') }}" class="h-10" alt="Logo Login">
    </div>
@else
    {{-- Logo untuk dashboard --}}
    <div class="flex items-center space-x-3">
        <img src="{{ asset('logo_upn.png') }}" class="h-8" alt="Logo Dashboard">
        <span class="text-sm font-semibold text-gray-800 p-4">Sistem Informasi <br>Management KMI</span>
    </div>
@endif
