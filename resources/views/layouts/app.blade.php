<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>{{ $title ?? 'BookMe' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Rýchle online rezervácie pre služby na jednom mieste.">

    <!-- Facebook Meta Tags -->
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? 'BookMe - Moderný rezervačný systém' }}">
    <meta property="og:description" content="Rýchle online rezervácie pre služby na jednom mieste. Zarezervujte si svoj termín bez telefonátov.">
    <meta property="og:image" content="{{ asset('og-image.png') }}">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta property="twitter:domain" content="{{ request()->getHost() }}">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $title ?? 'BookMe - Moderný rezervačný systém' }}">
    <meta name="twitter:description" content="Rýchle online rezervácie pre služby na jednom mieste. Zarezervujte si svoj termín bez telefonátov.">
    <meta name="twitter:image" content="{{ asset('og-image.png') }}">

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
            "Loading businesses...": "{{ __('Loading businesses...') }}",
            "No businesses found.": "{{ __('No businesses found.') }}",
            "Failed to load businesses.": "{{ __('Failed to load businesses.') }}",
            "Loading services...": "{{ __('Loading services...') }}",
            "No services found for this business.": "{{ __('No services found for this business.') }}",
            "Choose business": "{{ __('Choose business') }}",
            "Choose service": "{{ __('Choose service') }}",
            "Choose variant": "{{ __('Choose variant') }}",
            "Choose date and variant.": "{{ __('Choose date and variant.') }}",
            "Loading...": "{{ __('Loading...') }}",
            "No free slots for this day.": "{{ __('No free slots for this day.') }}",
            "Select": "{{ __('Select') }}",
            "Booking in progress...": "{{ __('Booking in progress...') }}",
            "Booking successful!": "{{ __('Booking successful!') }}",
            "Your appointment has been confirmed. We sent information to your email.": "{{ __('Your appointment has been confirmed. We sent information to your email.') }}",
            "Error": "{{ __('Error') }}",
            "Please select a time for your appointment.": "{{ __('Please select a time for your appointment.') }}",
            "Close": "{{ __('Close') }}",
            "soon": "{{ __('soon') }}",
            "today": "{{ __('today') }}",
            "tomorrow": "{{ __('tomorrow') }}",
            "Choose business and service.": "{{ __('Choose business and service.') }}",
            "Failed to load availability.": "{{ __('Failed to load availability.') }}",
            "Morning": "{{ __('Morning') }}",
            "Afternoon": "{{ __('Afternoon') }}",
            "Select time to lock slot.": "{{ __('Select time to lock slot.') }}",
            "Checking slot...": "{{ __('Checking slot...') }}",
            "Success": "{{ __('Success') }}",
            "Your appointment has been successfully booked. Check your email for information.": "{{ __('Your appointment has been successfully booked. Check your email for information.') }}",
            "Done": "{{ __('Done') }}",
            "Error booking appointment.": "{{ __('Error booking appointment.') }}",
            "locking": "{{ __('locking') }}",
            "busy": "{{ __('busy') }}",
            "free": "{{ __('free') }}",
            "Someone else is currently filling out a reservation": "{{ __('Someone else is currently filling out a reservation') }}",
            "Reset": "{{ __('Reset') }}",
            "day": "{{ __('day') }}",
            "days_2_4": "{{ __('days_2_4') }}",
            "days_5_more": "{{ __('days_5_more') }}",
            "Reservation at": "{{ __('Reservation at') }}",
            "Add to iOS calendar": "{{ __('Add to iOS calendar') }}",
            "Add to Android calendar": "{{ __('Add to Android calendar') }}",
            "reservation.ics": "{{ __('reservation.ics') }}",
            "None (use service base)": "{{ __('None (use service base)') }}",
            "Continue booking?": "{{ __('Continue booking?') }}",
            "Due to inactivity, your pending reservation will be cancelled soon.": "{{ __('Due to inactivity, your pending reservation will be cancelled soon.') }}",
            "Continue": "{{ __('Continue') }}",
            "Cancel": "{{ __('Cancel') }}",
            "No free slots for selected day.": "{{ __('No free slots for selected day.') }}",
            "Appointment confirmed:": "{{ __('Appointment confirmed:') }}",
            "price": "{{ __('price') }}",
            "Failed to load data.": "{{ __('Failed to load data.') }}",
            "Error during booking.": "{{ __('Error during booking.') }}",
            "Appointment booking": "{{ __('Appointment booking') }}",
            "Slot will be locked for 5 minutes": "{{ __('Slot will be locked for 5 minutes') }}",
            "Confirm booking": "{{ __('Confirm booking') }}",
            "Your name": "{{ __('Your name') }}",
            "Choose date": "{{ __('Choose date') }}",
            "Available times": "{{ __('Available times') }}",
            "Loading free slots...": "{{ __('Loading free slots...') }}",
            "Next slot": "{{ __('Next slot') }}",
            "at": "{{ __('at') }}",
            "No free slots available for this day.": "{{ __('No free slots available for this day.') }}",
            "All": "{{ __('All') }}"
        };
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/sk.js"></script>
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
    <div class="bg-grainy"></div>
    <div class="min-h-screen relative overflow-x-hidden">
        @if(session('status'))
            <script>
                window.onload = function() {
                    Swal.fire({
                        title: window.translations['Success'] || 'Úspech!',
                        text: "{{ session('status') }}",
                        icon: 'success',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true
                    });
                }
            </script>
        @endif
        @if(session('error'))
            <script>
                window.onload = function() {
                    Swal.fire({
                        title: window.translations['Error'] || 'Chyba!',
                        text: "{{ session('error') }}",
                        icon: 'error',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 6000,
                        timerProgressBar: true
                    });
                }
            </script>
        @endif
        <div id="header-wrapper" class="fixed top-0 left-0 right-0 z-50 translate-y-0">
            <header id="main-header" class="max-w-6xl mx-auto px-4 py-3 md:py-6 flex items-center justify-between gap-4 transition-all duration-300">
                <a href="/" id="logo" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-300 shadow-lg shadow-emerald-200/60 flex items-center justify-center text-slate-900 font-semibold">B</div>
                    <div>
                        <p class="font-display text-lg leading-tight">{{ __('BookMe') }}</p>
                        <p class="text-sm text-slate-500">{{ __('Bookings that fit into the day') }}</p>
                    </div>
                </a>
                <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-700 ml-auto">
                    <a class="hover:text-slate-900 transition" href="/#search">{{ __('Search business') }}</a>
                    <a class="hover:text-slate-900 transition" href="{{ route('for-businesses') }}">{{ __('For businesses') }}</a>
                    {{-- <a class="hover:text-slate-900 transition" href="{{ route('articles.index') }}">Blog</a> --}}
                    @auth
                        @if(auth()->user()->role === 'admin')
                            {{-- <a class="hover:text-slate-900 transition font-bold text-emerald-600" href="{{ route('admin.dashboard') }}">Admin</a> --}}
                        @else
                            <a class="hover:text-slate-900 transition font-bold text-emerald-600" href="{{ route('owner.dashboard') }}">{{ __('My business') }}</a>
                        @endif
                        <form method="POST" action="{{ route('auth.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="hover:text-red-600 transition font-medium text-slate-700">{{ __('Logout') }}</button>
                        </form>
                    @else
                        <a class="hover:text-slate-900 transition" href="{{ route('auth.login') }}">{{ __('Login') }}</a>
                    @endauth
                    @include('partials.language-switcher')
                </nav>
                <div class="hidden sm:flex gap-3 items-center">
                    <a href="#booking" class="px-6 py-2.5 text-sm font-bold rounded-full bg-emerald-500 text-white shadow-lg shadow-emerald-200/50 border border-emerald-400 hover:bg-emerald-600 hover:shadow-emerald-300/60 transition-all transform hover:scale-105">{{ __('Start booking') }}</a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center gap-3">
                    @include('partials.language-switcher')
                    <button id="mobile-menu-button" type="button" class="text-slate-700 hover:text-slate-900 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path id="mobile-menu-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </header>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-xl border border-slate-100 px-4 py-4 space-y-2 shadow-2xl mx-4">
                <a class="block py-2 text-base font-medium text-slate-700 hover:text-emerald-600" href="/#search">{{ __('Search business') }}</a>
                <a class="block py-2 text-base font-medium text-slate-700 hover:text-emerald-600" href="{{ route('for-businesses') }}">{{ __('For businesses') }}</a>
                {{-- <a class="block py-2 text-base font-medium text-slate-700 hover:text-emerald-600" href="{{ route('articles.index') }}">Blog</a> --}}
                @auth
                    @if(auth()->user()->role === 'admin')
                        {{-- <a class="block py-2 text-base font-bold text-emerald-600" href="{{ route('admin.dashboard') }}">Admin</a> --}}
                    @else
                        <a class="block py-2 text-base font-bold text-emerald-600" href="{{ route('owner.dashboard') }}">{{ __('My business') }}</a>
                    @endif
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left py-2 text-base font-medium text-red-600 hover:text-red-700">{{ __('Logout') }}</button>
                    </form>
                @else
                    <a class="block py-2 text-base font-medium text-slate-700 hover:text-emerald-600" href="{{ route('auth.login') }}">{{ __('Login') }}</a>
                @endauth
                <div class="pt-2 border-t border-slate-100">
                    <a href="#booking" class="block py-2 text-center text-sm font-semibold rounded-full bg-emerald-500 text-white shadow-sm hover:bg-emerald-600 transition">{{ __('Start booking') }}</a>
                </div>
            </div>
        </div>

        <main class="max-w-6xl mx-auto px-4 pt-25 md:pt-32">
            @yield('content')
        </main>

        <footer class="max-w-6xl mx-auto px-4 py-10 text-sm text-slate-600">
            <div class="flex flex-col md:flex-row justify-between gap-3 border-t border-slate-200 pt-6">
                <p>{{ __('BookMe — online reservations without phone calls.') }}</p>
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
