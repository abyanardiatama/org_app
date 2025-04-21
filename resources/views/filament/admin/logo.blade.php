@if (request()->routeIs('filament.admin.auth.login'))
    {{-- Logo untuk halaman login --}}
    <div class="flex items-center gap-x-4">
        <img src="{{ asset('logo_upn.png') }}" class="h-10 p-5" alt="Logo Login">
        <img src="{{ asset('logo_kmi.png') }}" class="h-10 p-5" alt="Logo Login">
    </div>
@else
    {{-- Logo untuk dashboard --}}
    {{-- <div class="flex items-center space-x-3">
        <img src="{{ asset('logo_upn.png') }}" class="h-8 pr-4" alt="Logo Dashboard">
        <img src="{{ asset('logo_kmi.png') }}" class="h-10 pl-5" alt="Logo Login">
        <span class="text-sm font-semibold text-gray-800 p-4">Sistem Informasi <br>Management KMI</span>
    </div> --}}
    <div class="flex items-center gap-x-3">
        <img src="{{ asset('logo_upn.png') }}" class="h-8" alt="Logo Dashboard">
        <img src="{{ asset('logo_kmi.png') }}" class="h-10" alt="Logo Login">
        <span class="text-sm font-semibold text-gray-800">
            Sistem Informasi <br>Management KMI
        </span>
    </div>
    
@endif
