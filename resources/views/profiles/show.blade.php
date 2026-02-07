@extends('layouts.app')

@section('content')
<div class="space-y-12 pb-20 overflow-x-hidden">
    <!-- Hero Banner & Logo -->
    <div class="relative h-72 md:h-96 w-full rounded-[40px] overflow-hidden shadow-2xl mt-8">
        @if($profile->banner_url)
            <img src="{{ $profile->banner_url }}" alt="{{ $profile->name }} banner" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center">
                <span class="text-white/10 text-9xl font-bold select-none">{{ $profile->name }}</span>
            </div>
        @endif

        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent"></div>

        <div class="absolute bottom-8 left-8 right-8 flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
            <div class="flex items-center gap-6 max-w-full">
                <div class="h-24 w-24 md:h-32 md:w-32 flex-shrink-0 rounded-3xl bg-transparent p-2 shadow-2xl border border-white/20 overflow-hidden transform hover:scale-105 transition-transform duration-500">
                    @if($profile->logo_url)
                        <img src="{{ $profile->logo_url }}" alt="{{ $profile->name }} logo" class="w-full h-full object-contain rounded-2xl">
                    @else
                        <div class="w-full h-full bg-slate-50 flex items-center justify-center rounded-2xl">
                            <span class="text-emerald-500 font-bold text-3xl">{{ substr($profile->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-3 mb-1">
                        <span class="px-3 py-1 rounded-full bg-emerald-500 text-white text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-emerald-500/40">{{ $profile->category }}</span>
                        <div class="flex items-center gap-1 text-white text-sm font-bold">
                            <svg class="w-4 h-4 text-orange-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            4.9
                        </div>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-display font-bold text-white drop-shadow-xl truncate">{{ $profile->name }}</h1>
                    <p class="text-white/80 font-medium flex items-center gap-2 mt-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $profile->city }}
                    </p>
                </div>
            </div>
            <button onclick="openBookingModal({{ $profile->id }}, null, '{{ __('General reservation') }}', false)" class="px-8 py-4 rounded-2xl bg-white text-slate-900 font-bold hover:bg-emerald-500 hover:text-white transition-all shadow-2xl hover:-translate-y-1 active:translate-y-0">
                {{ __('Book now') }}
            </button>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-10">
        <!-- Left Column: Description & Services Info -->
        <div class="lg:col-span-2 space-y-12">
            <section class="space-y-6">
                <h2 class="text-2xl font-display font-bold text-slate-900 flex items-center gap-3">
                    <span class="w-8 h-1 bg-emerald-500 rounded-full"></span>
                    {{ __('About us') }}
                </h2>
                <div class="text-slate-600 text-lg leading-relaxed whitespace-pre-line bg-white rounded-[32px] p-8 border border-slate-50 shadow-sm">
                    {{ $profile->description ?? __('Táto prevádzka zatiaľ nemá pridaný popis.') }}
                </div>
            </section>

            <section class="space-y-6">
                <h2 class="text-2xl font-display font-bold text-slate-900 flex items-center gap-3">
                    <span class="w-8 h-1 bg-emerald-500 rounded-full"></span>
                    {{ __('Our services') }}
                </h2>
                <div class="grid sm:grid-cols-1 gap-4">
                    @foreach($profile->services as $service)
                        <div class="group p-6 rounded-[32px] bg-white border border-slate-50 hover:border-emerald-100 transition-all shadow-sm hover:shadow-xl hover:shadow-emerald-200/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex-1">
                                <p class="font-bold text-xl text-slate-900 group-hover:text-emerald-600 transition-colors">{{ $service->name }}</p>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-sm font-medium text-slate-400 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $service->base_duration_minutes }} min
                                    </span>
                                    <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                                    <span class="text-sm font-bold text-emerald-600">od €{{ number_format($service->base_price, 2) }}</span>
                                </div>

                                @if($service->employees->count() > 0)
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach($service->employees as $employee)
                                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-50 border border-slate-100 shadow-sm">
                                                <div class="h-1.5 w-1.5 rounded-full bg-slate-400"></div>
                                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-tight">Zamestnanec: {{ $employee->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center gap-4">
                                <button onclick="openBookingModal({{ $profile->id }}, {{ $service->id }}, '{{ addslashes($service->name) }}', {{ $service->is_pakavoz_enabled ? 'true' : 'false' }})" class="p-4 rounded-[20px] bg-slate-50 text-slate-400 group-hover:bg-emerald-500 group-hover:text-white transition-all shadow-sm group-hover:shadow-lg group-hover:shadow-emerald-200 group-hover:-translate-y-0.5">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <!-- Right Column: Opening Hours & Location -->
        <div class="space-y-10">
            <!-- Opening Hours -->
            <section class="bg-white rounded-[40px] p-8 shadow-xl shadow-slate-200/60 border border-slate-50 space-y-6">
                <h2 class="text-xl font-display font-bold text-slate-900 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    {{ __('Opening hours') }}
                </h2>
                <div class="space-y-4">
                    @php
                        $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
                        $schedules = $profile->schedules->groupBy('day_of_week');
                    @endphp
                    @foreach($days as $dayNum => $dayName)
                        <div class="flex items-center justify-between text-sm group">
                            <span class="text-slate-500 font-medium group-hover:text-slate-900 transition-colors">{{ __($dayName) }}</span>
                            @if($schedules->has($dayNum))
                                <span class="text-slate-900 font-bold bg-slate-50 px-3 py-1 rounded-full group-hover:bg-emerald-50 transition-colors">
                                    {{ \Carbon\Carbon::parse($schedules[$dayNum]->first()->start_time)->format('H:i') }} —
                                    {{ \Carbon\Carbon::parse($schedules[$dayNum]->first()->end_time)->format('H:i') }}
                                </span>
                            @else
                                <span class="text-rose-400 font-medium italic">{{ __('Closed') }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Location & Map -->
            <section class="bg-white rounded-[40px] p-8 shadow-xl shadow-slate-200/60 border border-slate-50 space-y-6">
                <h2 class="text-xl font-display font-bold text-slate-900 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    {{ __('Where to find us') }}
                </h2>
                <div class="bg-slate-50 p-4 rounded-2xl">
                    <p class="font-bold text-slate-900">{{ $profile->address_line1 }}</p>
                    <p class="text-sm text-slate-500">{{ $profile->postal_code }} {{ $profile->city }}</p>
                </div>

                @php
                    $mapQuery = urlencode($profile->address_line1 . ', ' . $profile->city);
                    $latLong = ($profile->latitude && $profile->longitude) ? $profile->latitude . ',' . $profile->longitude : null;
                @endphp

                <div class="h-64 rounded-[32px] overflow-hidden border border-slate-100 shadow-inner group">
                    <iframe
                        width="100%"
                        height="100%"
                        frameborder="0"
                        style="border:0"
                        src="https://www.google.com/maps?q={{ $latLong ?? $mapQuery }}&output=embed"
                        class="grayscale hover:grayscale-0 transition-all duration-700"
                        allowfullscreen>
                    </iframe>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div id="bookingModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeBookingModal()"></div>

        <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-2xl p-6 md:p-8 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-[10px] uppercase font-bold text-emerald-600 tracking-widest mb-1">Rezervácia termínu</p>
                    <h3 class="text-2xl font-display font-semibold text-slate-900" id="modal_service_name">Služba</h3>
                </div>
                <button onclick="closeBookingModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form class="space-y-4" id="modal-booking-form">
                @csrf
                <input type="hidden" name="profile_id" id="modal_profile_id">
                <input type="hidden" name="service_id" id="modal_service_id">
                <input type="hidden" name="start_at" id="modal_start_at">
                <input type="hidden" name="date" id="modal_date" value="{{ date('Y-m-d') }}">
                <input type="hidden" name="employee_id" id="modal_employee_id">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="label !ml-1">Vaše meno</label>
                        <input name="customer_name" type="text" class="input-control" placeholder="Meno a priezvisko" required />
                    </div>
                    <div class="space-y-1">
                        <label class="label !ml-1">E-mail</label>
                        <input name="customer_email" type="email" class="input-control" placeholder="vas@email.sk" required />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="label !ml-1">Telefón</label>
                        <input name="customer_phone" id="modal_customer_phone" type="text" class="input-control" placeholder="+421..." required />
                    </div>
                    <div class="space-y-1">
                        <label class="label !ml-1">Poznámka</label>
                        <input name="notes" type="text" class="input-control" placeholder="Voliteľná poznámka" />
                    </div>
                </div>

                <div id="pakavoz-fields" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="label !ml-1">EČV (ŠPZ)</label>
                        <input name="evc" id="modal_evc" type="text" class="input-control" placeholder="napr. BA123XY" />
                    </div>
                    <div class="space-y-1">
                        <label class="label !ml-1">Model vozidla</label>
                        <input name="vehicle_model" type="text" class="input-control" placeholder="napr. Škoda Octavia" />
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6 pt-2">
                    <div class="space-y-3">
                        <label class="label !ml-1">Vyberte dátum</label>
                        <div class="date-calendar !shadow-none !border-slate-100" id="modal-calendar">
                            <div class="flex items-center justify-between mb-2">
                                <button type="button" class="cal-nav" id="modal-cal-prev">‹</button>
                                <div class="text-center cal-month text-sm" id="modal-cal-month">—</div>
                                <button type="button" class="cal-nav" id="modal-cal-next">›</button>
                            </div>
                            <div class="calendar-grid !gap-1" id="modal-cal-grid">
                                <div class="calendar-heading">po</div>
                                <div class="calendar-heading">ut</div>
                                <div class="calendar-heading">st</div>
                                <div class="calendar-heading">št</div>
                                <div class="calendar-heading">pi</div>
                                <div class="calendar-heading">so</div>
                                <div class="calendar-heading">ne</div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="label !ml-1">Dostupné časy</label>
                        <div class="max-h-[280px] overflow-y-auto pr-2 space-y-4" id="modal-time-grid">
                            <p class="text-sm text-slate-400 italic">Načítavam voľné termíny...</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6 border-t border-slate-50">
                    <div class="flex items-center gap-2 text-[11px] text-slate-400 uppercase font-bold tracking-tight">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Slot sa zamkne na 5 minút
                    </div>
                    <button type="submit" class="px-8 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold transition shadow-lg shadow-emerald-200/50">
                        Potvrdiť rezerváciu
                    </button>
                </div>
            </form>
            <div id="modal-booking-output" class="mt-4 text-sm text-center font-medium hidden"></div>
        </div>
    </div>
</div>

<script>
    let modalState = {
        calendarStart: null,
        closedDays: [],
        selectedDate: '{{ date('Y-m-d') }}',
        shopId: null,
        serviceId: null,
        lockToken: null
    };

    function openBookingModal(shopId, serviceId, serviceName, isPakavoz = false) {
        modalState.shopId = shopId;
        modalState.serviceId = serviceId;
        modalState.calendarStart = new Date();
        // Nastavenie na pondelok aktuálneho týždňa
        const day = modalState.calendarStart.getDay();
        const diff = modalState.calendarStart.getDate() - day + (day === 0 ? -6 : 1);
        modalState.calendarStart.setDate(diff);

        document.getElementById('modal_service_name').textContent = serviceName;
        document.getElementById('modal_profile_id').value = shopId;
        document.getElementById('modal_service_id').value = serviceId;
        document.getElementById('bookingModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        const pakavozFields = document.getElementById('pakavoz-fields');
        const evcInput = document.getElementById('modal_evc');
        if (isPakavoz) {
            pakavozFields.classList.remove('hidden');
            evcInput.setAttribute('required', 'required');
        } else {
            pakavozFields.classList.add('hidden');
            evcInput.removeAttribute('required');
        }

        fetchCalendarData();
        fetchModalAvailability();
    }

    function closeBookingModal() {
        document.getElementById('bookingModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('modal-booking-output').classList.add('hidden');
        document.getElementById('modal-booking-form').reset();
    }

    async function fetchCalendarData() {
        const start = modalState.calendarStart;
        const startIso = start.getFullYear() + '-' + String(start.getMonth() + 1).padStart(2, '0') + '-' + String(start.getDate()).padStart(2, '0');

        try {
            const response = await axios.post('/api/availability', {
                profile_id: modalState.shopId,
                service_id: modalState.serviceId,
                date: startIso,
                days: 7
            });
            modalState.closedDays = response.data.closed_days || [];
            updateModalCalendar();
        } catch (error) {
            console.error('Calendar data error', error);
            updateModalCalendar();
        }
    }

    function updateModalCalendar() {
        const grid = document.getElementById('modal-cal-grid');
        const monthLabel = document.getElementById('modal-cal-month');

        // Clear previous days
        grid.querySelectorAll('.calendar-day').forEach(d => d.remove());

        const start = new Date(modalState.calendarStart);
        const monthName = start.toLocaleString('sk-SK', { month: 'long', year: 'numeric' });
        monthLabel.textContent = monthName;

        for (let i = 0; i < 7; i++) {
            const day = new Date(start);
            day.setDate(start.getDate() + i);

            const dayEl = document.createElement('button');
            dayEl.type = 'button';
            const iso = day.getFullYear() + '-' + String(day.getMonth() + 1).padStart(2, '0') + '-' + String(day.getDate()).padStart(2, '0');
            const isToday = iso === new Date().getFullYear() + '-' + String(new Date().getMonth() + 1).padStart(2, '0') + '-' + String(new Date().getDate()).padStart(2, '0');
            const isSelected = iso === modalState.selectedDate;
            const isClosed = modalState.closedDays.includes(iso);

            dayEl.className = `calendar-day h-10 w-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all
                ${isSelected ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-200' :
                  isClosed ? 'bg-red-50 text-red-500 hover:bg-red-100' : 'hover:bg-emerald-50 text-slate-700'}`;

            if (isToday && !isSelected && !isClosed) dayEl.classList.add('border', 'border-emerald-200', 'text-emerald-600');
            if (isToday && isClosed) dayEl.classList.add('border', 'border-red-200');

            dayEl.textContent = day.getDate();
            dayEl.onclick = () => {
                modalState.selectedDate = iso;
                document.getElementById('modal_date').value = iso;
                updateModalCalendar();
                fetchModalAvailability();
            };

            grid.appendChild(dayEl);
        }
    }

    async function fetchModalAvailability() {
        const grid = document.getElementById('modal-time-grid');
        const translations = window.translations || {};
        grid.innerHTML = `<p class="text-sm text-slate-400 italic">${translations["Loading..."] || 'Načítavam...'}</p>`;

        try {
            const response = await axios.post('/api/availability', {
                profile_id: modalState.shopId,
                service_id: modalState.serviceId,
                date: modalState.selectedDate,
                days: 1
            });

            const slots = response.data.slots || [];
            if (slots.length === 0) {
                grid.innerHTML = `<p class="text-sm text-slate-500 italic">${translations["No free slots for this day."] || 'Žiadne voľné termíny na tento deň.'}</p>`;
                return;
            }

            grid.innerHTML = '';
            slots.forEach(slot => {
                const btn = document.createElement('button');
                btn.type = 'button';
                const time = new Date(slot.start_at).toLocaleTimeString('sk-SK', { hour: '2-digit', minute: '2-digit', hour12: false });

                btn.className = 'w-full p-3 rounded-2xl border border-slate-100 hover:border-emerald-200 hover:bg-emerald-50 transition-all flex items-center justify-between group';
                btn.innerHTML = `
                    <span class="font-bold text-slate-900">${time}</span>
                    <span class="text-[10px] font-bold uppercase text-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity">${translations["Select"] || 'Vybrať'}</span>
                `;

                btn.onclick = () => {
                    grid.querySelectorAll('button').forEach(b => b.classList.remove('border-emerald-500', 'bg-emerald-50', 'ring-2', 'ring-emerald-500/20'));
                    btn.classList.add('border-emerald-500', 'bg-emerald-50', 'ring-2', 'ring-emerald-500/20');
                    document.getElementById('modal_start_at').value = slot.start_at;

                    // Create lock
                    axios.post('/api/locks', {
                        profile_id: modalState.shopId,
                        service_id: modalState.serviceId,
                        start_at: slot.start_at,
                        date: modalState.selectedDate
                    }).then(response => {
                        modalState.lockToken = response.data.token;
                    }).catch(err => console.error('Lock error', err));
                };

                grid.appendChild(btn);
            });
        } catch (error) {
            grid.innerHTML = '<p class="text-sm text-red-500">Chyba pri načítaní dát.</p>';
        }
    }

    document.getElementById('modal-cal-prev').onclick = () => {
        modalState.calendarStart.setDate(modalState.calendarStart.getDate() - 7);
        fetchCalendarData();
    };
    document.getElementById('modal-cal-next').onclick = () => {
        modalState.calendarStart.setDate(modalState.calendarStart.getDate() + 7);
        fetchCalendarData();
    };

    document.getElementById('modal-booking-form').onsubmit = async (e) => {
        e.preventDefault();
        const translations = window.translations || {};
        const out = document.getElementById('modal-booking-output');
        const form = e.target;
        const startAt = document.getElementById('modal_start_at').value;

        if (!startAt) {
            Swal.fire(translations["Error"] || 'Chyba', translations["Please select a time for your appointment."] || 'Vyberte si prosím čas termínu.', 'error');
            return;
        }

        out.classList.remove('hidden', 'text-red-500', 'text-emerald-600');
        out.textContent = translations["Booking in progress..."] || 'Odosielam rezerváciu...';

        try {
            const formData = new FormData(form);
            const payload = Object.fromEntries(formData.entries());

            if (modalState.lockToken) {
                payload.lock_token = modalState.lockToken;
            }

            const response = await axios.post('/api/appointments', payload);
            out.classList.add('text-emerald-600');
            out.textContent = translations["Booking successful!"] || 'Rezervácia bola úspešná!';

            Swal.fire({
                title: translations["Booking successful!"] || 'Rezervácia úspešná!',
                text: translations["Your appointment has been confirmed. We sent information to your email."] || 'Váš termín bol potvrdený. Informácie sme vám zaslali e-mailom.',
                icon: 'success',
                confirmButtonColor: '#10b981'
            }).then(() => {
                closeBookingModal();
                location.reload();
            });

        } catch (error) {
            out.classList.add('text-red-500');
            const errors = error.response?.data?.errors;
            let message = error.response?.data?.message || 'Chyba pri rezervácii.';
            if (errors) {
                message = Object.values(errors).flat().join(' ');
            }
            out.textContent = message;
        }
    };
</script>
@endsection
