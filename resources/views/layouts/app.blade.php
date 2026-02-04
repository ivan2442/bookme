<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>{{ $title ?? 'BookMe' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Rýchle online rezervácie pre služby na jednom mieste.">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Nice Select -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js"></script>
    <style>
        select.nice-select {
            display: none !important;
        }
        .nice-select {
            border-radius: 12px !important;
            border: 1px solid #f1f5f9 !important;
            height: 46px !important;
            line-height: 44px !important;
            padding-left: 16px !important;
            padding-right: 30px !important;
            font-size: 0.9rem !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            background-color: #ffffff !important;
            width: 100% !important;
            float: none !important;
            display: flex !important;
            align-items: center !important;
        }
        .nice-select:after {
            right: 16px !important;
        }
        .nice-select .current {
            display: block !important;
            width: 100% !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            line-height: 44px !important;
        }
        .nice-select .list {
            width: 100% !important;
            border-radius: 12px !important;
            max-height: 200px !important;
            overflow-y: auto !important;
            z-index: 9999 !important;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1) !important;
        }
        .nice-select-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
        }
        .nice-select-wrapper:has(.nice-select.open) {
            z-index: 50;
        }
        .nice-select-time {
            text-align: center !important;
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .nice-select-time .current {
            padding-right: 0 !important;
            text-align: center !important;
        }
        .nice-select-time:after {
            display: none !important;
        }
        .nice-select-time .option {
            line-height: 24px !important;
            min-height: 24px !important;
            padding-top: 4px !important;
            padding-bottom: 4px !important;
            text-align: center !important;
        }
        .nice-select .option.busy-option {
            color: #94a3b8 !important;
            background-color: #f8fafc !important;
            font-style: italic;
        }
        .nice-select .option.busy-option:hover {
            background-color: #f1f5f9 !important;
            color: #64748b !important;
        }
    </style>
</head>
<body class="bg-sand text-slate-900 selection:bg-emerald-200/80 selection:text-slate-900 overflow-x-hidden">
    <div class="bg-gradient radial-fade"></div>
    <div class="min-h-screen relative overflow-x-hidden">
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
        <div id="header-wrapper" class="fixed top-0 left-0 right-0 z-50 transition-transform duration-300 translate-y-0">
            <header id="main-header" class="max-w-6xl mx-auto px-4 py-4 md:py-6 flex items-center justify-between gap-4 bg-sand/80 backdrop-blur-md rounded-b-[20px] md:rounded-none transition-all duration-300">
                <a href="/" id="logo" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-300 shadow-lg shadow-emerald-200/60 flex items-center justify-center text-slate-900 font-semibold">B</div>
                    <div>
                        <p class="font-display text-lg leading-tight">BookMe</p>
                        <p class="text-sm text-slate-500">Rezervácie, ktoré zapadnú do dňa</p>
                    </div>
                </a>
                <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-700 ml-auto">
                    <a class="hover:text-slate-900 transition" href="/#search">Vyhľadať prevádzku</a>
                    <a class="hover:text-slate-900 transition" href="/#services">Služby</a>
                    {{-- <a class="hover:text-slate-900 transition" href="{{ route('articles.index') }}">Blog</a> --}}
                    @auth
                        @if(auth()->user()->role === 'admin')
                            {{-- <a class="hover:text-slate-900 transition font-bold text-emerald-600" href="{{ route('admin.dashboard') }}">Admin</a> --}}
                        @else
                            <a class="hover:text-slate-900 transition font-bold text-emerald-600" href="{{ route('owner.dashboard') }}">Moja prevádzka</a>
                        @endif
                        <form method="POST" action="{{ route('auth.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="hover:text-red-600 transition font-medium text-slate-700">Odhlásiť sa</button>
                        </form>
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
            <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-slate-200 px-4 py-4 space-y-2 mt-2 shadow-xl rounded-b-2xl mx-4">
                <a class="block py-2 text-base font-medium text-slate-700 hover:text-emerald-600" href="/#search">Vyhľadať prevádzku</a>
                <a class="block py-2 text-base font-medium text-slate-700 hover:text-emerald-600" href="/#services">Služby</a>
                {{-- <a class="block py-2 text-base font-medium text-slate-700 hover:text-emerald-600" href="{{ route('articles.index') }}">Blog</a> --}}
                @auth
                    @if(auth()->user()->role === 'admin')
                        {{-- <a class="block py-2 text-base font-bold text-emerald-600" href="{{ route('admin.dashboard') }}">Admin</a> --}}
                    @else
                        <a class="block py-2 text-base font-bold text-emerald-600" href="{{ route('owner.dashboard') }}">Moja prevádzka</a>
                    @endif
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left py-2 text-base font-medium text-red-600 hover:text-red-700">Odhlásiť sa</button>
                    </form>
                @else
                    <a class="block py-2 text-base font-medium text-slate-700 hover:text-emerald-600" href="{{ route('auth.login') }}">Prihlásiť sa</a>
                @endauth
                <div class="pt-2 border-t border-slate-100">
                    <a href="#booking" class="block py-2 text-center text-sm font-semibold rounded-full bg-emerald-500 text-white shadow-sm hover:bg-emerald-600 transition">Začať rezerváciu</a>
                </div>
            </div>
        </div>

        <main class="max-w-6xl mx-auto px-4 pt-28 md:pt-32">
            @yield('content')
        </main>

        <footer class="max-w-6xl mx-auto px-4 py-10 text-sm text-slate-600">
            <div class="flex flex-col md:flex-row justify-between gap-3 border-t border-slate-200 pt-6">
                <p>BookMe — online rezervácie bez telefonátov.</p>
                <p class="text-slate-500">&copy 2026 WsTechnology.dev All Rights Reserved</p>
            </div>
        </footer>

        <!-- Scroll to Top Button -->
        <button id="scroll-to-top" class="fixed bottom-[25px] right-[25px] w-[25px] h-[25px] bg-emerald-500 text-white rounded-[6px] shadow-lg flex items-center justify-center opacity-0 invisible transition-all duration-300 hover:bg-emerald-600 z-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
            </svg>
        </button>
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
