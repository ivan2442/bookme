<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>{{ $title ?? 'BookMe Partner' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <script>
        window.locale = "{{ app()->getLocale() }}";
        window.translations = {
            "mon": "{{ __('mon') }}",
            "tue": "{{ __('tue') }}",
            "wed": "{{ __('wed') }}",
            "thu": "{{ __('thu') }}",
            "fri": "{{ __('fri') }}",
            "sat": "{{ __('sat') }}",
            "sun": "{{ __('sun') }}",
            "Appointments for today": "{{ __('Appointments for today') }}",
            "Appointments for": "{{ __('Appointments for') }}",
            "Today": "{{ __('Today') }}",
            "Monday": "{{ __('Monday') }}",
            "Tuesday": "{{ __('Tuesday') }}",
            "Wednesday": "{{ __('Wednesday') }}",
            "Thursday": "{{ __('Thursday') }}",
            "Friday": "{{ __('Friday') }}",
            "Saturday": "{{ __('Saturday') }}",
            "Sunday": "{{ __('Sunday') }}",
            "Completed": "{{ __('Completed') }}",
            "Confirmed": "{{ __('Confirmed') }}",
            "Pending": "{{ __('Pending') }}",
            "Confirm": "{{ __('Confirm') }}",
            "Mark as completed": "{{ __('Mark as completed') }}",
            "Reschedule": "{{ __('Reschedule') }}",
            "Delete": "{{ __('Delete') }}",
            "Are you sure you want to delete this appointment?": "{{ __('Are you sure you want to delete this appointment?') }}",
            "No upcoming appointments for this day.": "{{ __('No upcoming appointments for this day.') }}"
        };
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body class="bg-sand text-slate-900 selection:bg-emerald-200/80 selection:text-slate-900 overflow-x-hidden">
    <div class="bg-gradient radial-fade"></div>
    <div class="bg-grainy"></div>

    <div class="min-h-screen flex relative">
        <!-- Sidebar -->
        <aside id="owner-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white/80 backdrop-blur-xl border-r border-slate-100 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="flex flex-col h-full">
                <div class="p-6">
                    <a href="/" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-300 shadow-lg shadow-emerald-200/60 flex items-center justify-center text-slate-900 font-semibold text-lg">B</div>
                        <div>
                            <p class="font-display text-lg leading-tight">BookMe</p>
                            <p class="text-[10px] uppercase font-bold tracking-widest text-emerald-600">Partner Panel</p>
                        </div>
                    </a>
                </div>

                <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
                    <p class="px-3 text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-2 mt-4">{{ __('Prehľad') }}</p>
                    <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('owner.dashboard') ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('owner.appointments') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('owner.appointments') ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ __('Appointments') }}
                    </a>

                    <p class="px-3 text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-2 mt-6">{{ __('Správa prevádzky') }}</p>
                    <a href="{{ route('owner.services') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('owner.services') ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ __('Services') }}
                    </a>
                    <a href="{{ route('owner.employees') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('owner.employees') ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        {{ __('Employees') }}
                    </a>
                    <a href="{{ route('owner.schedules') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('owner.schedules') ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Working hours') }}
                    </a>
                    <a href="{{ route('owner.holidays') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('owner.holidays') ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ __('Holidays') }}
                    </a>

                    <p class="px-3 text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-2 mt-6">{{ __('Nastavenia') }}</p>
                    <a href="{{ route('owner.calendar.settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('owner.calendar.settings') ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ __('Calendar') }}
                    </a>
                    <a href="{{ route('owner.billing.settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('owner.billing.settings') ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        {{ __('Billing details') }}
                    </a>

                    <p class="px-3 text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-2 mt-6">{{ __('Financie') }}</p>
                    <a href="{{ route('owner.payments') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('owner.payments') ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Revenue') }}
                    </a>
                    <a href="{{ route('owner.invoices') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('owner.invoices') ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        {{ __('Subscription invoices') }}
                    </a>
                </nav>

                <div class="p-4 border-t border-slate-100">
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-sm font-medium text-rose-600 hover:bg-rose-50 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 lg:pl-64">
            <!-- Top Bar -->
            <header class="h-16 bg-white/60 backdrop-blur-md border-b border-slate-100 sticky top-0 z-40 flex items-center justify-between px-4 sm:px-8">
                <div class="flex items-center gap-4">
                    <button id="owner-sidebar-toggle" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 transition">
                        <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h2 class="font-display font-bold text-slate-800 hidden sm:block">Partner Dashboard</h2>
                </div>

                <div class="flex items-center gap-4">
                    @include('partials.language-switcher')
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold text-slate-900 leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Majiteľ prevádzky</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-emerald-400 to-emerald-200 border-2 border-white shadow-md flex items-center justify-center text-white font-bold text-xs uppercase">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 p-4 sm:p-8">
                @if(session('status'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 text-sm font-medium shadow-sm animate-fade-in">
                        {{ session('status') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-800 text-sm font-medium shadow-sm animate-fade-in">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Sidebar toggle for mobile
        const sidebar = document.getElementById('owner-sidebar');
        const toggle = document.getElementById('owner-sidebar-toggle');

        if (toggle && sidebar) {
            toggle.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
            });

            // Close when clicking outside on mobile
            document.addEventListener('click', (e) => {
                if (window.innerWidth < 1024 &&
                    sidebar &&
                    !sidebar.contains(e.target) &&
                    !toggle.contains(e.target) &&
                    !sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.add('-translate-x-full');
                }
            });
        }

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

                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
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
