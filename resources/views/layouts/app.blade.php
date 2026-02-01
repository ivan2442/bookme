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
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-700">
                <a class="hover:text-slate-900 transition" href="/#search">Vyhľadať prevádzku</a>
                <a class="hover:text-slate-900 transition" href="/#services">Služby</a>
                <a class="hover:text-slate-900 transition" href="{{ route('articles.index') }}">Blog</a>
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
                <a href="#booking" class="px-4 py-2 text-sm font-semibold rounded-full bg-white shadow-sm border border-white/60 hover:border-emerald-200 hover:shadow-lg transition">Začať rezerváciu</a>
            </div>
        </header>

        <main class="max-w-6xl mx-auto px-4">
            @yield('content')
        </main>

        <footer class="max-w-6xl mx-auto px-4 py-10 text-sm text-slate-600">
            <div class="flex flex-col md:flex-row justify-between gap-3 border-t border-slate-200 pt-6">
                <p>BookMe — online rezervácie bez telefonátov.</p>
                <p class="text-slate-500">Laravel 11 · MySQL · Sanctum · Tailwind</p>
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
