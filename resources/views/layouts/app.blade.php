<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'BookMe' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Rýchle online rezervácie pre služby na jednom mieste.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-sand text-slate-900 selection:bg-emerald-200/80 selection:text-slate-900">
    <div class="bg-gradient radial-fade"></div>
    <div class="min-h-screen relative overflow-hidden">
        @if(session('status'))
            <script>
                window.onload = function() {
                    Swal.fire({
                        title: 'Úspech!',
                        text: "{{ session('status') }}",
                        icon: 'success',
                        confirmButtonColor: '#10b981'
                    });
                }
            </script>
        @endif
        @if(session('error'))
            <script>
                window.onload = function() {
                    Swal.fire({
                        title: 'Chyba!',
                        text: "{{ session('error') }}",
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                }
            </script>
        @endif
        <header class="max-w-6xl mx-auto px-4 pt-6 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-300 shadow-lg shadow-emerald-200/60 flex items-center justify-center text-slate-900 font-semibold">B</div>
                <div>
                    <p class="font-display text-lg leading-tight">BookMe</p>
                    <p class="text-sm text-slate-500">Rezervácie, ktoré zapadnú do dňa</p>
                </div>
            </div>
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-700 ml-auto">
                <a class="hover:text-slate-900 transition" href="/#search">Vyhľadať prevádzku</a>
                <a class="hover:text-slate-900 transition" href="/#services">Služby</a>
                {{-- <a class="hover:text-slate-900 transition" href="{{ route('articles.index') }}">Blog</a> --}}
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a class="hover:text-slate-900 transition font-bold text-emerald-600" href="{{ route('admin.dashboard') }}">Admin</a>
                    @else
                        <a class="hover:text-slate-900 transition font-bold text-emerald-600" href="{{ route('owner.dashboard') }}">Moja prevádzka</a>
                    @endif
                @else
                    <a class="hover:text-slate-900 transition" href="{{ route('auth.login') }}">Prihlásiť sa</a>
                @endauth
            </nav>
            <div class="hidden sm:flex gap-3 items-center">
                <a href="#booking" class="px-6 py-2.5 text-sm font-bold rounded-full bg-emerald-500 text-white shadow-lg shadow-emerald-200/50 border border-emerald-400 hover:bg-emerald-600 hover:shadow-emerald-300/60 transition-all transform hover:scale-105">Začať rezerváciu</a>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-button" type="button" class="text-slate-700 hover:text-slate-900 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path id="mobile-menu-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </header>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-slate-200 px-4 py-4 space-y-2 mt-4">
            <a class="block py-2 text-base font-medium text-slate-700 hover:text-emerald-600" href="/#search">Vyhľadať prevádzku</a>
            <a class="block py-2 text-base font-medium text-slate-700 hover:text-emerald-600" href="/#services">Služby</a>
            {{-- <a class="block py-2 text-base font-medium text-slate-700 hover:text-emerald-600" href="{{ route('articles.index') }}">Blog</a> --}}
            @auth
                @if(auth()->user()->role === 'admin')
                    <a class="block py-2 text-base font-bold text-emerald-600" href="{{ route('admin.dashboard') }}">Admin</a>
                @else
                    <a class="block py-2 text-base font-bold text-emerald-600" href="{{ route('owner.dashboard') }}">Moja prevádzka</a>
                @endif
            @else
                <a class="block py-2 text-base font-medium text-slate-700 hover:text-emerald-600" href="{{ route('auth.login') }}">Prihlásiť sa</a>
            @endauth
            <div class="pt-2 border-t border-slate-100">
                <a href="#booking" class="block py-2 text-center text-sm font-semibold rounded-full bg-emerald-500 text-white shadow-sm hover:bg-emerald-600 transition">Začať rezerváciu</a>
            </div>
        </div>

        <main class="max-w-6xl mx-auto px-4">
            @yield('content')
        </main>

        <footer class="max-w-6xl mx-auto px-4 py-10 text-sm text-slate-600">
            <div class="flex flex-col md:flex-row justify-between gap-3 border-t border-slate-200 pt-6">
                <p>BookMe — online rezervácie bez telefonátov.</p>
                <p class="text-slate-500">&copy 2026 WsTechnology.dev All Rights Reserved</p>
            </div>
        </footer>
    </div>
    <script>
        function confirmDelete(event, message = 'Naozaj chcete odstrániť túto položku?') {
            event.preventDefault();
            const form = event.target.closest('form');
            const button = event.target.closest('button');

            Swal.fire({
                title: 'Ste si istý?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#475569',
                confirmButtonText: 'Áno, odstrániť!',
                cancelButtonText: 'Zrušiť'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (form) {
                        form.submit();
                    } else if (button && button.hasAttribute('formaction')) {
                        const newForm = document.createElement('form');
                        newForm.method = 'POST';
                        newForm.action = button.getAttribute('formaction');

                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken;

                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = button.getAttribute('formmethod') || 'DELETE';

                        newForm.appendChild(csrfInput);
                        newForm.appendChild(methodInput);
                        document.body.appendChild(newForm);
                        newForm.submit();
                    }
                }
            });
            return false;
        }
    </script>
</body>
</html>
