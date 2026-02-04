import './bootstrap';
import moment from 'moment';
import Lightpick from 'lightpick';
import 'lightpick/css/lightpick.css';


const cityInput = document.getElementById('filter-city');
const categorySelect = document.getElementById('filter-category');
const queryInput = document.getElementById('filter-query');
const shopList = document.querySelector('[data-shop-list]');
const servicesList = document.querySelector('[data-services-list]');
const shopSelect = document.querySelector('[data-shop-select]');
const serviceSelect = document.querySelector('[data-service-select]');
const variantSelect = document.querySelector('[data-variant-select]');
const variantWrapper = document.querySelector('[data-variant-wrapper]');
const timeGrid = document.querySelector('[data-time-grid]');
const timeInput = document.querySelector('[data-time-input]');
const dateInput = document.querySelector('[data-date-input]');
const employeeInput = document.querySelector('[data-employee-input]');
const bookingForm = document.querySelector('[data-booking-form]');
const bookingOutput = document.querySelector('[data-booking-output]');
const timePlaceholder = document.querySelector('[data-time-placeholder]');
const calendarGrid = document.querySelector('[data-cal-grid]');
const calendarMonth = document.querySelector('[data-cal-month]');
const calendarPrev = document.querySelector('[data-cal-prev]');
const calendarNext = document.querySelector('[data-cal-next]');
const toggleAdvanced = document.getElementById('toggle-advanced');
const advancedFilters = document.getElementById('advanced-filters');
const advancedIcon = document.getElementById('advanced-icon');

if (toggleAdvanced && advancedFilters) {
    toggleAdvanced.addEventListener('click', () => {
        const isOpen = advancedFilters.classList.contains('opacity-100');
        if (isOpen) {
            advancedFilters.classList.replace('max-h-[500px]', 'max-h-0');
            advancedFilters.classList.replace('opacity-100', 'opacity-0');
            advancedFilters.classList.remove('mt-4');
            advancedIcon.classList.remove('rotate-180');
        } else {
            advancedFilters.classList.replace('max-h-0', 'max-h-[500px]');
            advancedFilters.classList.replace('opacity-0', 'opacity-100');
            advancedFilters.classList.add('mt-4');
            advancedIcon.classList.add('rotate-180');
        }
    });
}

const TIME_ZONE = 'Europe/Bratislava';

const timeFormatter = new Intl.DateTimeFormat('sk-SK', {
    hour: '2-digit',
    minute: '2-digit',
    timeZone: TIME_ZONE,
});

const dateTimeFormatter = new Intl.DateTimeFormat('sk-SK', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    timeZone: TIME_ZONE,
});

function formatIsoDate(date) {
    const year = date.toLocaleString('en-US', { year: 'numeric', timeZone: TIME_ZONE });
    const month = date.toLocaleString('en-US', { month: '2-digit', timeZone: TIME_ZONE });
    const day = date.toLocaleString('en-US', { day: '2-digit', timeZone: TIME_ZONE });
    return `${year}-${month}-${day}`;
}

let state = {
    shops: [],
    services: [],
    serviceById: {},
    variantsByService: {},
    employeesByVariant: {},
    variantMap: {},
    calendarStart: null,
    closedDays: [],
};

function formatRelativeSlot(isoDate) {
    if (!isoDate) return 'čoskoro';
    const date = new Date(isoDate);
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    const target = new Date(date.getFullYear(), date.getMonth(), date.getDate());

    const time = timeFormatter.format(date);

    if (target.getTime() === today.getTime()) {
        return `dnes ${time}`;
    } else if (target.getTime() === tomorrow.getTime()) {
        return `zajtra ${time}`;
    } else {
        const d = date.getDate().toString().padStart(2, '0');
        const m = (date.getMonth() + 1).toString().padStart(2, '0');
        return `${d}.${m}. ${time}`;
    }
}

function applyFilters() {
    const city = cityInput?.value.toLowerCase().trim() ?? '';
    const category = categorySelect?.value.toLowerCase().trim() ?? '';
    const query = queryInput?.value.toLowerCase().trim() ?? '';

    shopList?.querySelectorAll('[data-shop-card]').forEach((card) => {
        const cardCity = (card.dataset.city || '').toLowerCase();
        const cardCategory = (card.dataset.category || '').toLowerCase();
        const cardName = (card.dataset.name || '').toLowerCase();

        const matchesCity = city === '' || cardCity.includes(city);
        const matchesCategory = category === '' || cardCategory === category;
        const matchesQuery = query === '' || cardName.includes(query);

        card.style.display = matchesCity && matchesCategory && matchesQuery ? '' : 'none';
    });
}

cityInput?.addEventListener('input', applyFilters);
categorySelect?.addEventListener('change', applyFilters);
queryInput?.addEventListener('input', applyFilters);

function renderShops() {
    if (!shopList) return;
    if (!state.shops.length) {
        shopList.innerHTML = '<p class="text-sm text-slate-500">Žiadne prevádzky nenájdené.</p>';
        return;
    }

    shopList.innerHTML = '';
    state.shops.forEach((shop) => {
        const card = document.createElement('div');
        card.className = 'shop-card cursor-pointer overflow-hidden border-0 bg-white shadow-lg shadow-slate-200/50 hover:shadow-2xl hover:shadow-emerald-200/40 hover:-translate-y-1 transition-all group duration-300';
        card.dataset.shopCard = '1';
        card.dataset.id = shop.id;
        card.dataset.city = shop.city || '';
        card.dataset.category = shop.category || '';
        card.dataset.name = (shop.name || '').toLowerCase();

        const rating = shop.rating ?? '4.8';
        const nextSlot = formatRelativeSlot(shop.next_slot);

        const bannerHtml = shop.banner_path
            ? `<div class="h-32 w-full overflow-hidden">
                 <img src="/storage/${shop.banner_path}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="${shop.name}">
               </div>`
            : `<div class="h-32 w-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center">
                 <span class="text-white/20 text-3xl font-bold">${shop.name}</span>
               </div>`;

        card.innerHTML = `
            ${bannerHtml}
            <div class="p-5 min-w-0">
                <div class="flex items-center justify-between mb-2 gap-2">
                    <span class="text-[10px] uppercase font-bold tracking-widest text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md truncate">${shop.category ?? ''}</span>
                    <div class="flex items-center gap-1 text-xs font-bold text-slate-900 flex-shrink-0">
                        <svg class="w-3 h-3 text-orange-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        ${rating}
                    </div>
                </div>
                <h3 class="font-display text-lg text-slate-900 group-hover:text-emerald-600 transition-colors mb-1 font-bold truncate">${shop.name}</h3>
                <div class="flex items-center gap-1.5 text-slate-400 text-xs mb-4 truncate">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="truncate">${shop.city ?? ''}</span>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-50 gap-2">
                    <div class="flex flex-col min-w-0">
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-tight leading-none mb-1">Najbližší termín</span>
                        <span class="text-sm font-bold text-slate-900 truncate">${nextSlot}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="/prevadzka/${shop.slug}" class="stop-propagation h-8 px-3 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-emerald-50 hover:text-emerald-600 transition-colors text-[10px] font-bold uppercase tracking-tight">
                            Detail
                        </a>
                        <div class="h-8 w-8 rounded-full bg-slate-900 text-white flex items-center justify-center group-hover:bg-emerald-500 transition-colors flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                </div>
            </div>
        `;

        card.addEventListener('click', (e) => {
            if (e.target.closest('.stop-propagation')) {
                return;
            }
            const shopId = shop.id;
            if (shopSelect && shopId) {
                shopSelect.value = shopId;
                populateServicesForShop(shopId);
                renderServices(shopId);
            }

            const servicesSection = document.getElementById('services');
            if (servicesSection) {
                servicesSection.classList.remove('hidden');
                servicesSection.scrollIntoView({ behavior: 'smooth' });
            }
        });

        shopList.appendChild(card);
    });

    applyFilters();
}

function populateCategorySelect() {
    if (!categorySelect) return;
    const categories = Array.from(new Set(state.shops.map((shop) => shop.category).filter(Boolean)));
    categorySelect.innerHTML = '<option value="">Všetky</option>';
    categories.forEach((category) => {
        const opt = document.createElement('option');
        opt.value = category;
        opt.textContent = category;
        categorySelect.appendChild(opt);
    });
}

function renderServices(shopId = null) {
    if (!servicesList) return;

    let servicesToRender = state.services;
    if (shopId) {
        servicesToRender = state.services.filter((s) => String(s.profile_id) === String(shopId));
    }

    if (!servicesToRender.length) {
        servicesList.innerHTML = '<p class="text-sm text-slate-500">Žiadne služby nenájdené pre túto prevádzku.</p>';
        return;
    }

    servicesList.innerHTML = '';
    servicesToRender.forEach((service) => {
        const duration = service.base_duration_minutes ?? 30;
        const price = service.base_price ?? 0;
        const employeeNames =
            (service.employees || []).map((e) => e.name).join(', ') ||
            '';

        const card = document.createElement('div');
        card.className = 'service-card';
        card.dataset.serviceCard = '1';
        card.dataset.shopId = service.profile_id;
        card.dataset.serviceId = service.id;
        card.innerHTML = `
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="font-semibold text-lg text-slate-900 truncate">${service.name}</p>
                </div>
                <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold uppercase tracking-wider flex-shrink-0">${service.category ?? ''}</span>
            </div>
            <div class="flex items-center justify-between mt-2 text-xs text-slate-500 gap-2">
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>${duration} min</span>
                </div>
                <span class="font-bold text-emerald-600 text-sm flex-shrink-0">€${Number(price).toFixed(2)}</span>
            </div>
            ${
                employeeNames
                    ? `<div class="mt-3 flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-100 w-full overflow-hidden">
                         <div class="h-1.5 w-1.5 rounded-full bg-slate-400 flex-shrink-0"></div>
                         <p class="text-[10px] font-bold text-slate-600 uppercase tracking-tight truncate">Zamestnanec: ${employeeNames}</p>
                       </div>`
                    : ''
            }
            <button class="mt-3 px-3 py-2 w-full rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition shadow-sm" data-choose-service="${service.id}">
                Vybrať termín
            </button>
        `;
        servicesList.appendChild(card);
    });

    servicesList.querySelectorAll('[data-choose-service]').forEach((button) => {
        button.addEventListener('click', () => {
            const serviceId = button.dataset.chooseService;
            const service = state.services.find((s) => String(s.id) === String(serviceId));
            if (serviceSelect && serviceId) {
                serviceSelect.value = serviceId;
                if (shopSelect) {
                    shopSelect.value = service.profile_id;
                }
                populateVariants(serviceId);
            }

            const bookingSection = document.getElementById('booking');
            if (bookingSection) {
                bookingSection.classList.remove('hidden');
                bookingSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

function populateShopSelect() {
    if (!shopSelect) return;
    shopSelect.innerHTML = '<option value="">Vyber prevádzku</option>';
    state.shops.forEach((shop) => {
        const opt = document.createElement('option');
        opt.value = shop.id;
        opt.textContent = `${shop.name} — ${shop.city ?? ''}`;
        shopSelect.appendChild(opt);
    });
}

function populateServiceSelect() {
    if (!serviceSelect) return;
    serviceSelect.innerHTML = '<option value="">Vyber službu</option>';
    state.services.forEach((service) => {
        const opt = document.createElement('option');
        opt.value = service.id;
        opt.textContent = `${service.name} (${service.shop_name ?? ''})`;
        serviceSelect.appendChild(opt);
    });
}

function populateServicesForShop(shopId) {
    if (!serviceSelect) return;
    serviceSelect.innerHTML = '<option value="">Vyber službu</option>';
    state.services
        .filter((s) => String(s.profile_id) === String(shopId))
        .forEach((service) => {
            const opt = document.createElement('option');
            opt.value = service.id;
            opt.textContent = `${service.name} (${service.shop_name ?? ''})`;
            serviceSelect.appendChild(opt);
        });
}

function populateVariants(serviceId) {
    if (!variantSelect) return;
    const variants = state.variantsByService[serviceId] || [];
    variantSelect.innerHTML = '<option value="">Žiadny (použiť základ služby)</option>';
    variants.forEach((variant) => {
        const opt = document.createElement('option');
        opt.value = variant.id;
        opt.textContent = `${variant.name} — ${variant.duration_minutes} min (€${Number(variant.price ?? 0).toFixed(2)})`;
        variantSelect.appendChild(opt);
    });

    if (variants[0]) {
        variantSelect.value = variants[0].id;
        assignEmployeeForVariant(variants[0].id);
        fetchAvailability(true);
    } else {
        if (variantWrapper) {
            variantWrapper.style.display = 'none';
        }
        variantSelect.value = '';
        assignEmployeeForService(serviceId);
        fetchAvailability(true);
        return;
    }

    if (variantWrapper) {
        variantWrapper.style.display = '';
    }
}

function assignEmployeeForVariant(variantId) {
    if (!employeeInput) return;
    const employees = state.employeesByVariant[variantId] || [];
    employeeInput.value = employees[0]?.id ?? '';
}

function assignEmployeeForService(serviceId) {
    if (!employeeInput) return;
    const service = state.serviceById[serviceId];
    const employees =
        service?.employees ||
        [];
    employeeInput.value = employees[0]?.id ?? '';
}

async function fetchShops() {
    try {
        const response = await axios.get('/api/shops', { params: { per_page: 50 } });
        const data = response.data?.data ?? response.data ?? [];
        state.shops = data.map((shop) => ({
            ...shop,
            shop_name: shop.name,
        }));
        state.services = [];
        state.serviceById = {};
        state.variantsByService = {};
        state.employeesByVariant = {};
        state.variantMap = {};

        state.shops.forEach((shop) => {
            (shop.services || []).forEach((service) => {
                if (!service.is_active) {
                    return;
                }
                const employees = (service.employees && service.employees.length > 0) ? service.employees : (shop.employees || []);
                state.services.push({
                    ...service,
                    profile_id: shop.id,
                    shop_name: shop.name,
                    base_price: service.base_price,
                    base_duration_minutes: service.base_duration_minutes,
                    employees,
                });
                state.serviceById[service.id] = {
                    ...service,
                    profile_id: shop.id,
                    shop_name: shop.name,
                    employees,
                };
                state.variantsByService[service.id] = service.variants || [];
                (service.variants || []).forEach((variant) => {
                    state.employeesByVariant[variant.id] = variant.employees || [];
                    state.variantMap[variant.id] = {
                        ...variant,
                        service_id: service.id,
                    };
                });
            });
        });

        renderShops();
        renderServices();
        populateShopSelect();
        populateServiceSelect();
        populateCategorySelect();
    } catch (error) {
        console.error('Nepodarilo sa načítať prevádzky', error);
        if (shopList) {
            shopList.innerHTML = '<p class="text-sm text-red-500">Nepodarilo sa načítať prevádzky.</p>';
        }
    }
}

async function fetchWeekAvailability() {
    if (!shopSelect?.value || !serviceSelect?.value || !state.calendarStart) {
        return;
    }

    const startIso = formatIsoDate(state.calendarStart);

    try {
        const response = await axios.post('/api/availability', {
            profile_id: shopSelect.value,
            service_id: serviceSelect.value,
            service_variant_id: variantSelect.value || null,
            employee_id: employeeInput?.value || null,
            date: startIso,
            days: 7,
        });

        if (response.data?.closed_days) {
            state.closedDays = response.data.closed_days;
            renderCalendar();
        }
    } catch (error) {
        console.error('Chyba pri načítaní týždennej dostupnosti', error);
    }
}

async function fetchAvailability(autoSelectNearest = false) {
    if (!shopSelect?.value || !serviceSelect?.value) {
        resetSlots('Vyber prevádzku a službu.');
        return;
    }

    let date = dateInput?.value || formatIsoDate(new Date());

    try {
        const response = await axios.post('/api/availability', {
            profile_id: shopSelect.value,
            service_id: serviceSelect.value,
            service_variant_id: variantSelect.value || null,
            employee_id: employeeInput?.value || null,
            date,
            days: autoSelectNearest ? 14 : 1,
        });

    let slots = response.data?.slots ?? [];
        const closedDays = response.data?.closed_days ?? [];

        // Update state with closed days if we received them
        closedDays.forEach(d => {
            if (!state.closedDays.includes(d)) state.closedDays.push(d);
        });

        if (autoSelectNearest && slots.length > 0) {
            const firstAvailableSlot = slots.find(s => s.status === 'available');
            if (firstAvailableSlot) {
                const availableDate = firstAvailableSlot.start_at.split('T')[0];
                if (availableDate !== date) {
                    date = availableDate;
                    if (dateInput) dateInput.value = date;

                    // Update calendar UI range if necessary
                    const start = startOfWeek(new Date(date));
                    if (state.calendarStart && state.calendarStart.getTime() !== start.getTime()) {
                        state.calendarStart = start;
                    }
                }
            }
        }

        // Filter slots for the selected date only
        const filteredSlots = slots.filter(s => s.start_at.startsWith(date));

        renderCalendar();
        renderSlots(filteredSlots);
    } catch (error) {
        console.error('Chyba pri načítaní dostupnosti', error);
        resetSlots('Nepodarilo sa načítať dostupnosť.');
    }
}

function resetSlots(message) {
    if (!timeGrid) return;
    timeGrid.innerHTML = `<span class="text-sm text-slate-500">${message}</span>`;
    timeInput && (timeInput.value = '');
}

let lockTimeout = null;
    let refreshTimeout = null;

    const startLockTimer = () => {
        if (lockTimeout) clearTimeout(lockTimeout);
        if (refreshTimeout) clearTimeout(refreshTimeout);

        // 4 minutes and 50 seconds = 290000 ms (10 seconds before 5 minutes)
        lockTimeout = setTimeout(() => {
            Swal.fire({
                title: 'Pokračovať v rezervácii?',
                text: 'Z dôvodu neaktivity bude vaša rozpracovaná rezervácia čoskoro zrušená.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#f43f5e',
                confirmButtonText: 'Pokračovať',
                cancelButtonText: 'Zrušiť',
                timer: 10000,
                timerProgressBar: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    // Extend the lock (client-side simple version, just restart timer)
                    startLockTimer();
                } else {
                    window.location.reload();
                }
            });
        }, 290000);

        // 5 minutes = 300000 ms
        refreshTimeout = setTimeout(() => {
            if (!Swal.isVisible()) {
                window.location.reload();
            }
        }, 300000);
    };

    function renderSlots(slots) {
        if (!timeGrid) return;
        if (!slots.length) {
            resetSlots('Žiadne voľné termíny pre vybraný deň.');
            return;
        }
        timeGrid.innerHTML = '';

        const morning = [];
        const afternoon = [];

        slots.forEach((slot) => {
            const startDate = new Date(slot.start_at);
            const hour = parseInt(
                new Intl.DateTimeFormat('en-GB', { hour: '2-digit', hour12: false, timeZone: TIME_ZONE }).format(startDate),
                10
            );
            if (hour < 12) {
                morning.push(slot);
            } else {
                afternoon.push(slot);
            }
        });

        const renderGroup = (label, group) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'slot-group max-w-full overflow-hidden';
            const heading = document.createElement('p');
            heading.className = 'slot-group-heading mb-3 truncate';
            heading.textContent = label;
            wrapper.appendChild(heading);

            const row = document.createElement('div');
            row.className = 'flex flex-row gap-2 sm:gap-3 overflow-x-auto pb-2 scrollbar-hide max-w-full w-full';

            group.forEach((slot) => {
                const button = document.createElement('button');
                const start = new Date(slot.start_at);
                const time = timeFormatter.format(start);
                const isAvailable = slot.status === 'available';
                const isLocking = slot.status === 'locking';

                button.type = 'button';
                button.className = 'slot-card min-w-[85px] sm:min-w-[100px] flex-shrink-0 text-center transition-all hover:border-emerald-200 p-2.5 sm:p-3 overflow-hidden';
                button.dataset.time = slot.start_at;

                const timeP = document.createElement('p');
                timeP.className = 'font-semibold text-slate-900 truncate';
                timeP.textContent = time;

                const statusP = document.createElement('p');
                statusP.className = 'text-[10px] text-emerald-600 truncate';

                if (isLocking) {
                    button.disabled = true;
                    button.dataset.status = 'busy';
                    button.classList.add('opacity-70', 'cursor-not-allowed');
                    statusP.classList.replace('text-emerald-600', 'text-orange-500');
                    statusP.textContent = 'obsadzuje sa';
                    button.title = 'Niekto iný práve vypĺňa rezerváciu';
                } else if (!isAvailable) {
                    button.disabled = true;
                    button.dataset.status = 'busy';
                    button.classList.add('opacity-60', 'cursor-not-allowed');
                    statusP.classList.replace('text-emerald-600', 'text-slate-500');
                    statusP.textContent = 'obsadené';
                    button.title = 'Obsadené';
                } else {
                    statusP.textContent = 'voľný';
                    button.addEventListener('click', () => {
                        timeGrid.querySelectorAll('.slot-card').forEach((b) => {
                            b.classList.remove('is-active', 'ring-2', 'ring-emerald-500', 'border-emerald-500', 'bg-emerald-50');
                            // Reset internal text colors
                            const tp = b.querySelector('p.font-semibold');
                            if (tp) {
                                tp.classList.remove('text-white');
                                tp.classList.add('text-slate-900');
                            }
                            const sp = b.querySelector('p.text-\\[10px\\]');
                            if (sp) {
                                sp.classList.remove('text-emerald-100');
                                sp.classList.add('text-emerald-600');
                            }
                        });

                        button.classList.add('is-active', 'ring-2', 'ring-emerald-500', 'border-emerald-500', 'bg-emerald-500');
                        // Light up text
                        if (timeP) {
                            timeP.classList.remove('text-slate-900');
                            timeP.classList.add('text-white');
                        }
                        if (statusP) {
                            statusP.classList.remove('text-emerald-600');
                            statusP.classList.add('text-emerald-100');
                        }

                        if (timeInput) {
                            timeInput.value = slot.start_at;
                        }

                        // Create temporary lock
                        const payload = {
                            profile_id: shopSelect.value,
                            service_id: serviceSelect.value,
                            service_variant_id: variantSelect?.value || null,
                            employee_id: employeeInput?.value || null,
                            start_at: slot.start_at,
                            date: dateInput?.value
                        };

                        axios.post('/api/locks', payload)
                            .then(() => {
                                startLockTimer();
                            })
                            .catch(err => console.error('Nepodarilo sa vytvoriť zámok', err));
                    });
                }

                button.appendChild(timeP);
                button.appendChild(statusP);
                row.appendChild(button);
            });

            wrapper.appendChild(row);
            timeGrid.appendChild(wrapper);
        };

        if (morning.length) {
            renderGroup('Dopoludnie', morning);
        }
        if (afternoon.length) {
            renderGroup('Popoludnie', afternoon);
        }
    }

bookingForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    if (!bookingOutput) {
        console.warn('Booking output element not found');
        return;
    }

    bookingOutput.classList.remove('hidden');

    const formData = new FormData(bookingForm);
    const payload = Object.fromEntries(formData.entries());

    if (!payload.start_at) {
        bookingOutput.textContent = 'Vyber čas, aby sme zamkli slot.';
        return;
    }

    bookingOutput.textContent = `Overujem slot...`;

    const selectedVariant = state.variantMap[payload.service_variant_id];
    const selectedService =
        state.serviceById[selectedVariant?.service_id] ||
        state.services.find((s) => String(s.id) === String(payload.service_id));
    const durationMinutes = selectedService?.base_duration_minutes || 30;
    const price = selectedService?.base_price ?? 0;
    let endTimeReadable = '';
    try {
        const startDate = new Date(payload.start_at);
        const endDate = new Date(startDate.getTime() + durationMinutes * 60000);
        endTimeReadable = `${timeFormatter.format(startDate)} - ${timeFormatter.format(endDate)}`;
    } catch (e) {
        endTimeReadable = '';
    }

    axios
        .post('/api/appointments', {
            profile_id: payload.shop_id,
            service_id: payload.service_id,
            service_variant_id: payload.service_variant_id,
            employee_id: payload.employee_id || null,
            start_at: payload.start_at,
            date: payload.date || null,
            customer_name: payload.customer_name,
            customer_email: payload.customer_email,
            customer_phone: payload.customer_phone,
            notes: payload.notes,
        })
        .then((response) => {
            const appointment = response.data;
            const successMessage = `Termín potvrdený: ${appointment.service?.name ?? 'Služba'} ${dateTimeFormatter.format(
                new Date(appointment.start_at),
            )} ${endTimeReadable ? `(${endTimeReadable})` : ''}, cena €${Number(price).toFixed(2)}.`;

            bookingOutput.textContent = successMessage;

            if (typeof Swal !== 'undefined') {
                const title = appointment.service?.name ?? 'Služba';
                const start = appointment.start_at;
                const shopName = state.shops.find(s => String(s.id) === String(payload.shop_id))?.name ?? 'BookMe';

                Swal.fire({
                    title: 'Rezervácia úspešná!',
                    html: `
                        <p class="mb-4">${successMessage}</p>
                        <div class="flex flex-col gap-2 mt-4">
                            <button onclick="downloadIcs('${title}', '${start}', ${durationMinutes}, '${shopName}')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-900 font-semibold transition flex items-center justify-center gap-2">
                                📱 Pridať do iOS kalendára
                            </button>
                            <button onclick="openGoogleCalendar('${title}', '${start}', ${durationMinutes}, '${shopName}')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-900 font-semibold transition flex items-center justify-center gap-2">
                                🤖 Pridať do Android kalendára
                            </button>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonColor: '#10b981',
                    confirmButtonText: 'Zavrieť'
                }).then(() => {
                    // Reset formy
                    if (bookingForm) {
                        bookingForm.reset();
                        bookingOutput.textContent = 'Vyber službu a čas, systém preverí dostupnosť a potvrdí ti termín.';
                        bookingOutput.classList.add('hidden');
                    }

                    // Skryť sekcie kroku 2 a 3
                    const servicesSection = document.getElementById('services');
                    const bookingSection = document.getElementById('booking');
                    if (servicesSection) servicesSection.classList.add('hidden');
                    if (bookingSection) bookingSection.classList.add('hidden');

                    // Reload stránky pre úplný reset
                    location.reload();
                });
            }
        })
        .catch((error) => {
            console.error('Chyba pri rezervácii', error);
            const errors = error.response?.data?.errors;
            const message = error.response?.data?.message || (errors ? JSON.stringify(errors) : 'Nepodarilo sa vytvoriť rezerváciu.');
    bookingOutput.textContent = message;
        });
});

shopSelect?.addEventListener('change', () => {
    const shopId = shopSelect.value;
    if (shopId) {
        populateServicesForShop(shopId);
        fetchWeekAvailability();
    } else {
        populateServiceSelect();
    }
    variantSelect && (variantSelect.innerHTML = '<option value=\"\">Vyber variant</option>');
    resetSlots('Vyber variant.');
});

serviceSelect?.addEventListener('change', () => {
    const serviceId = serviceSelect.value;
    if (serviceId) {
        populateVariants(serviceId);
        fetchWeekAvailability();
    } else {
        variantSelect && (variantSelect.innerHTML = '<option value=\"\">Vyber variant</option>');
        resetSlots('Vyber službu.');
    }
});

variantSelect?.addEventListener('change', () => {
    if (variantSelect.value) {
        assignEmployeeForVariant(variantSelect.value);
        fetchAvailability();
    } else {
        assignEmployeeForService(serviceSelect.value);
        fetchAvailability();
    }
});
dateInput?.addEventListener('change', fetchAvailability);

// Scroll to search section on "Začať rezerváciu" click
document.querySelectorAll('a[href="#booking"]').forEach(link => {
    link.addEventListener('click', (e) => {
        const searchSection = document.getElementById('search');
        if (searchSection) {
            e.preventDefault();
            searchSection.scrollIntoView({ behavior: 'smooth' });
        }

        // Close mobile menu if open
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.add('hidden');
        }
    });
});

// Mobile menu toggle
const mobileMenuButton = document.getElementById('mobile-menu-button');
const mobileMenu = document.getElementById('mobile-menu');
if (mobileMenuButton && mobileMenu) {
    const toggleMenu = () => {
        const isOpen = !mobileMenu.classList.contains('hidden');
        if (isOpen) {
            mobileMenu.classList.add('hidden');
        } else {
            mobileMenu.classList.remove('hidden');
        }
    };

    mobileMenuButton.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleMenu();
    }, { passive: false });

    // Close mobile menu when clicking on any link inside it
    mobileMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
        }, { passive: true });
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!mobileMenu.classList.contains('hidden') &&
            !mobileMenu.contains(e.target) &&
            !mobileMenuButton.contains(e.target)) {
            mobileMenu.classList.add('hidden');
        }
    }, { passive: true });
}

// Scroll to Top Logic
const scrollToTopBtn = document.getElementById('scroll-to-top');
if (scrollToTopBtn) {
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            scrollToTopBtn.classList.remove('opacity-0', 'invisible');
            scrollToTopBtn.classList.add('opacity-100', 'visible');
        } else {
            scrollToTopBtn.classList.add('opacity-0', 'invisible');
            scrollToTopBtn.classList.remove('opacity-100', 'visible');
        }
    }, { passive: true });

    scrollToTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

fetchShops();

function startOfWeek(date) {
    const d = new Date(date);
    const day = (d.getDay() + 6) % 7; // Monday=0
    d.setDate(d.getDate() - day);
    d.setHours(0, 0, 0, 0);
    return d;
}

function setSelectedDate(date, updateCalendarRange = false) {
    if (dateInput) {
        const iso = formatIsoDate(date);
        dateInput.value = iso;
    }
    if (updateCalendarRange) {
        state.calendarStart = startOfWeek(date);
    }
    fetchAvailability();
    fetchWeekAvailability();
    renderCalendar();
}

function renderCalendar() {
    if (!calendarGrid || !calendarMonth) return;
    const today = new Date();
    if (!state.calendarStart) {
        state.calendarStart = startOfWeek(today);
    }
    const start = state.calendarStart;
    const monthFormatter = new Intl.DateTimeFormat('sk-SK', { month: 'long', year: 'numeric', timeZone: TIME_ZONE });
    calendarMonth.textContent = monthFormatter.format(start);

    calendarGrid.querySelectorAll('.calendar-item').forEach((el) => el.remove());

    for (let i = 0; i < 7; i++) {
        const day = new Date(start);
        day.setDate(start.getDate() + i);
        const dayNum = day.getDate();
        const iso = formatIsoDate(day);
        const isPast = day < new Date(today.toDateString());
        const isClosed = state.closedDays.includes(iso);

        const item = document.createElement('a');
        item.href = '#';
        item.className = 'calendar-item overflow-hidden';
        item.textContent = dayNum;
        if (isPast) {
            item.classList.add('disabled');
        }
        if (isClosed) {
            item.classList.add('closed');
        }
        if (dateInput?.value === iso || (!dateInput?.value && i === 0)) {
            item.classList.add('active');
        }

        item.addEventListener('click', (e) => {
            e.preventDefault();
            if (isPast || isClosed) return;
            setSelectedDate(day);
        });

        calendarGrid.appendChild(item);
    }
}

function bindCalendarNav() {
    if (!calendarPrev || !calendarNext) return;
    calendarPrev.addEventListener('click', (e) => {
        e.preventDefault();
        const newDate = new Date(state.calendarStart);
        newDate.setDate(newDate.getDate() - 7);
        const today = new Date();
        if (newDate < startOfWeek(today)) {
            state.calendarStart = startOfWeek(today);
        } else {
            state.calendarStart = newDate;
        }
        renderCalendar();
        fetchWeekAvailability();
    });
    calendarNext.addEventListener('click', (e) => {
        e.preventDefault();
        const newDate = new Date(state.calendarStart);
        newDate.setDate(newDate.getDate() + 7);
        state.calendarStart = newDate;
        renderCalendar();
        fetchWeekAvailability();
    });
}

function initSelectedDate() {
    const today = new Date();
    state.calendarStart = startOfWeek(today);
    if (dateInput && !dateInput.value) {
        dateInput.value = formatIsoDate(today);
    }

    // Pomocné funkcie pre kalendár
    window.downloadIcs = function(title, start, duration, shopName) {
        const startDate = new Date(start);
        const endDate = new Date(startDate.getTime() + duration * 60000);

        const format = (d) => d.toISOString().replace(/-|:|\.\d+/g, '');

        const icsMsg = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'BEGIN:VEVENT',
            `DTSTART:${format(startDate)}`,
            `DTEND:${format(endDate)}`,
            `SUMMARY:${title}`,
            `DESCRIPTION:Rezervácia v ${shopName}`,
            `LOCATION:${shopName}`,
            'END:VEVENT',
            'END:VCALENDAR'
        ].join('\n');

        const blob = new Blob([icsMsg], { type: 'text/calendar;charset=utf-8' });
        const link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.setAttribute('download', 'rezervacia.ics');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    window.openGoogleCalendar = function(title, start, duration, shopName) {
        const startDate = new Date(start);
        const endDate = new Date(startDate.getTime() + duration * 60000);

        const format = (d) => d.toISOString().replace(/-|:|\.\d+/g, '');

        const url = `https://www.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(title)}&dates=${format(startDate)}/${format(endDate)}&details=${encodeURIComponent('Rezervácia v ' + shopName)}&location=${encodeURIComponent(shopName)}`;
        window.open(url, '_blank');
    };

    // Lightpick initialization
    const dateInputs = document.querySelectorAll('input[name="date"]:not([type="hidden"])');
    dateInputs.forEach(el => {
        new Lightpick({
            field: el,
            format: 'YYYY-MM-DD',
            lang: 'sk',
            locale: {
                buttons: {
                    prev: '←',
                    next: '→',
                    close: '×',
                    reset: 'Vynulovať',
                },
                tooltip: {
                    one: 'deň',
                    few: 'dni',
                    many: 'dní',
                },
                pluralize: function(i, locale) {
                    if (i === 1) return locale.tooltip.one;
                    if (i >= 2 && i <= 4) return locale.tooltip.few;
                    return locale.tooltip.many;
                },
            },
            onSelect: function(date) {
                if (el.dataset.dateInput !== undefined) {
                    // Ak je to hlavný kalendár na home page
                    setSelectedDate(date.toDate());
                }
            }
        });
    });
}

// ...existing initializers
initSelectedDate();
renderCalendar();
bindCalendarNav();
initAdminDashboard();

function initAdminDashboard() {
    const adminCalGrid = document.querySelector('[data-admin-cal-grid]');
    const adminCalMonth = document.querySelector('[data-admin-cal-month]');
    const adminCalPrev = document.querySelector('[data-admin-cal-prev]');
    const adminCalNext = document.querySelector('[data-admin-cal-next]');
    const adminDateInput = document.getElementById('admin-selected-date');
    const appointmentsList = document.getElementById('appointments-list');
    const upcomingTitle = document.getElementById('upcoming-title');

    if (!adminCalGrid || !adminDateInput) return;

    let adminState = {
        calendarStart: startOfWeek(new Date()),
        selectedDate: formatIsoDate(new Date())
    };

    function renderAdminCalendar() {
        if (!adminCalGrid || !adminCalMonth) return;
        const start = adminState.calendarStart;
        const monthFormatter = new Intl.DateTimeFormat('sk-SK', { month: 'long', year: 'numeric', timeZone: TIME_ZONE });
        adminCalMonth.textContent = monthFormatter.format(start);

        adminCalGrid.querySelectorAll('.calendar-item').forEach((el) => el.remove());

        for (let i = 0; i < 7; i++) {
            const day = new Date(start);
            day.setDate(start.getDate() + i);
            const dayNum = day.getDate();
            const iso = formatIsoDate(day);

            const item = document.createElement('a');
            item.href = '#';
            item.className = 'calendar-item overflow-hidden';
            item.textContent = dayNum;

            if (adminState.selectedDate === iso) {
                item.classList.add('active');
            }

            item.addEventListener('click', (e) => {
                e.preventDefault();
                adminState.selectedDate = iso;
                adminDateInput.value = iso;
                fetchAdminAppointments(iso);
                renderAdminCalendar();
            });

            adminCalGrid.appendChild(item);
        }
    }

    async function fetchAdminAppointments(date) {
        if (!appointmentsList) return;

        // Predbežný vizuálny feedback
        const d = new Date(date);
        const dayNames = ['Nedeľa', 'Pondelok', 'Utorok', 'Streda', 'Štvrtok', 'Piatok', 'Sobota'];
        const formattedTitle = (d.toDateString() === new Date().toDateString()) ? 'Termíny na dnes' : `Termíny na ${d.getDate()}.${d.getMonth()+1}.${d.getFullYear()} ${dayNames[d.getDay()]}`;
        if (upcomingTitle) upcomingTitle.textContent = formattedTitle;

        try {
            const response = await axios.get(`/owner/appointments/day?date=${date}`);
            const appointments = response.data;

            if (appointments.length === 0) {
                appointmentsList.innerHTML = `
                    <div class="py-8 text-center" id="no-appointments">
                        <p class="text-sm text-slate-500 italic">Žiadne nadchádzajúce rezervácie na tento deň.</p>
                    </div>`;
                return;
            }

            appointmentsList.innerHTML = appointments.map(a => `
                <div class="py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <p class="font-bold text-slate-900 leading-tight">${a.service_name}</p>
                            <span class="flex-shrink-0 text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold uppercase tracking-tight">${a.employee_name}</span>
                        </div>
                        <div class="flex flex-col text-sm text-slate-600">
                            <span class="font-medium text-slate-900">${a.customer_name}</span>
                            ${a.customer_phone ? `<span class="text-xs text-slate-500">${a.customer_phone}</span>` : ''}
                        </div>
                    </div>
                    <div class="md:text-right space-y-2 flex-1">
                        <div class="flex flex-col md:items-end">
                            <p class="font-bold text-slate-900 leading-none">${a.date_formatted} ${a.day_name}</p>
                            <div class="flex items-center gap-3 mt-1 md:justify-end">
                                <p class="text-sm font-semibold text-emerald-600">${a.start_time} - ${a.end_time}</p>
                                <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                                <p class="text-sm font-bold text-slate-900">€${a.price}</p>
                            </div>
                            <p class="text-[10px] uppercase font-bold text-slate-400 mt-1">${a.status}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 md:justify-end">
                            ${(a.status !== 'completed' && a.status !== 'no-show') ? `
                                <form method="POST" action="${a.status_update_url}">
                                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="p-2 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all shadow-sm flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-tight" title="Vybavené">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Vybavené</span>
                                    </button>
                                </form>
                                <form method="POST" action="${a.status_update_url}">
                                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                    <input type="hidden" name="status" value="no-show">
                                    <button type="submit" class="p-2 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-500 hover:text-white transition-all shadow-sm flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-tight" title="Neprišiel">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        <span>Neprišiel</span>
                                    </button>
                                </form>
                                <button type="button"
                                    onclick="openRescheduleModal(${a.id}, '${a.date_raw}', '${a.start_time}', ${a.duration_minutes}, ${a.employee_id || 'null'})"
                                    class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-500 hover:text-white transition-all shadow-sm flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-tight" title="Presunúť">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    <span>Presunúť</span>
                                </button>
                                <button type="button"
                                    onclick="openEditAppointmentModal(${JSON.stringify({
                                        id: a.id,
                                        customer_name: a.customer_name,
                                        customer_phone: a.customer_phone,
                                        service_name: a.service_name,
                                        date: a.date_raw,
                                        start_time: a.start_time,
                                        duration_minutes: a.duration_minutes,
                                        price: a.price,
                                        employee_id: a.employee_id,
                                        notes: a.notes
                                    }).replace(/"/g, '&quot;')})"
                                    class="p-2 rounded-lg bg-slate-50 text-slate-600 hover:bg-slate-500 hover:text-white transition-all shadow-sm flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-tight" title="Upraviť">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Upraviť</span>
                                </button>
                            ` : ''}
                            <form method="POST" action="${a.delete_url}" onsubmit="return confirmDelete(event, 'Naozaj vymazať túto rezerváciu?')">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition-all shadow-sm flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-tight" title="Vymazať">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Vymazať</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            `).join('');

        } catch (error) {
            console.error('Chyba pri načítaní rezervácií', error);
            appointmentsList.innerHTML = '<p class="text-sm text-red-500">Chyba pri načítaní dát.</p>';
        }
    }

    if (adminCalPrev) {
        adminCalPrev.addEventListener('click', (e) => {
            e.preventDefault();
            const newDate = new Date(adminState.calendarStart);
            newDate.setDate(newDate.getDate() - 7);
            adminState.calendarStart = newDate;
            renderAdminCalendar();
        });
    }

    if (adminCalNext) {
        adminCalNext.addEventListener('click', (e) => {
            e.preventDefault();
            const newDate = new Date(adminState.calendarStart);
            newDate.setDate(newDate.getDate() + 7);
            adminState.calendarStart = newDate;
            renderAdminCalendar();
        });
    }

    renderAdminCalendar();
}
