import './bootstrap';
import moment from 'moment';

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
const homePakavozFields = document.getElementById('home-pakavoz-fields');
const homeEvcInput = document.getElementById('home_evc');
const bookingOutput = document.querySelector('[data-booking-output]');
const timePlaceholder = document.querySelector('[data-time-placeholder]');
const calendarGrid = document.querySelector('[data-cal-grid]');
const calendarMonth = document.querySelector('[data-cal-month]');
const calendarPrev = document.querySelector('[data-cal-prev]');
const calendarNext = document.querySelector('[data-cal-next]');
const toggleAdvanced = document.getElementById('toggle-advanced');
const advancedFilters = document.getElementById('advanced-filters');
const advancedIcon = document.getElementById('advanced-icon');

let activeBookingRequests = 0;
function showBookingLoading() {
    const loader = document.getElementById('booking-loading');
    if (loader) {
        activeBookingRequests++;
        loader.classList.remove('hidden');
    }
}

function hideBookingLoading() {
    const loader = document.getElementById('booking-loading');
    if (loader) {
        activeBookingRequests--;
        if (activeBookingRequests <= 0) {
            activeBookingRequests = 0;
            loader.classList.add('hidden');
        }
    }
}

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

window.timeFormatter = new Intl.DateTimeFormat('sk-SK', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
    timeZone: TIME_ZONE,
});

window.dateTimeFormatter = new Intl.DateTimeFormat('sk-SK', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
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
    closedDays: window.initialClosedDays || [],
    lockToken: null,
};

window.adminState = {
    calendarStart: null,
    selectedDate: null,
    closedDays: window.adminInitialClosedDays || []
};

function formatRelativeSlot(isoDate) {
    const translations = window.translations || {};
    if (!isoDate) return translations["soon"] || 'čoskoro';
    const date = new Date(isoDate);
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    const target = new Date(date.getFullYear(), date.getMonth(), date.getDate());

    const time = window.timeFormatter.format(date);

    if (target.getTime() === today.getTime()) {
        return `${translations["today"] || 'dnes'} ${time}`;
    } else if (target.getTime() === tomorrow.getTime()) {
        return `${translations["tomorrow"] || 'zajtra'} ${time}`;
    } else {
        const d = date.getDate().toString().padStart(2, '0');
        const m = (date.getMonth() + 1).toString().padStart(2, '0');
        return `${d}.${m}. ${time}`;
    }
}

window.formatFullNextSlot = function(isoDate) {
    const translations = window.translations || {};
    if (!isoDate) return translations["soon"] || 'čoskoro';
    const date = new Date(isoDate);

    const dayNames = [
        translations["Sunday"] || 'Nedeľa',
        translations["Monday"] || 'Pondelok',
        translations["Tuesday"] || 'Utorok',
        translations["Wednesday"] || 'Streda',
        translations["Thursday"] || 'Štvrtok',
        translations["Friday"] || 'Piatok',
        translations["Saturday"] || 'Sobota'
    ];

    const dayName = dayNames[date.getDay()];
    const d = date.getDate();
    const m = date.getMonth() + 1;
    const time = window.timeFormatter.format(date);
    const at = translations["at"] || 'o';

    return `${dayName} ${d}.${m}. ${at} ${time}`;
};

window.loadServicesNextSlots = async function() {
    const buttons = document.querySelectorAll('[data-next-slot-button]');
    if (!buttons.length) return;

    const shopId = document.getElementById('profile-data')?.dataset.profileId;
    if (!shopId) return;

    const translations = window.translations || {};
    const prefix = translations["Next slot"] || 'Najbližší termín';

    buttons.forEach(async (button) => {
        const serviceId = button.dataset.nextSlotButton;
        const textElement = button.querySelector('[data-next-slot-text]');

        try {
            const response = await axios.post('/api/availability', {
                profile_id: shopId,
                service_id: serviceId,
                date: formatIsoDate(new Date()),
                days: 35
            });

            const slots = response.data.slots || [];
            const firstAvailable = slots.find(s => s.status === 'available');

            if (firstAvailable) {
                const formatted = window.formatFullNextSlot(firstAvailable.start_at);
                if (textElement) {
                    textElement.textContent = `${prefix} ${formatted}`;
                }
            } else {
                if (textElement) textElement.textContent = translations["No free slots available for this day."] || 'Žiadne voľné termíny';
            }
            if (textElement) {
                textElement.classList.remove('opacity-0');
            }
        } catch (error) {
            console.error('Error loading next slot for service ' + serviceId, error);
            if (textElement) {
                textElement.textContent = translations["Failed to load data."] || 'Chyba pri načítaní dát.';
                textElement.classList.remove('opacity-0');
            }
        }
    });
};

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
    const translations = window.translations || {};
    if (!state.shops.length) {
        shopList.innerHTML = `<p class="text-sm text-slate-500">${translations["No businesses found."] || 'Žiadne prevádzky nenájdené.'}</p>`;
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

        const bannerHtml = shop.banner_url
            ? `<div class="h-52 w-full overflow-hidden">
                 <img src="${shop.banner_url}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="${shop.name}">
               </div>`
            : `<div class="h-52 w-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center">
                 <span class="text-white/20 text-3xl font-bold">${shop.name}</span>
               </div>`;

        card.innerHTML = `
            ${bannerHtml}
            <div class="p-5 min-w-0">
                <div class="flex flex-col gap-1.5 mb-2">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] uppercase font-bold tracking-widest text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md truncate">${shop.category ?? ''}</span>
                        <div class="flex items-center gap-1 text-xs font-bold text-slate-900 flex-shrink-0">
                            <svg class="w-3 h-3 text-orange-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            ${rating}
                        </div>
                    </div>
                    <h3 class="font-display text-lg text-slate-900 group-hover:text-emerald-600 transition-colors font-bold leading-snug">${shop.name}</h3>
                </div>
                <div class="flex items-center gap-1.5 text-slate-400 text-xs mb-4 truncate">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="truncate">${shop.city ?? ''}</span>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-50 gap-2">
                    <div class="flex flex-col min-w-0">
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-tight leading-none mb-1">${translations["Next slot"] || 'Najbližší termín'}</span>
                        <span class="text-sm font-bold text-slate-900 truncate">${nextSlot}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="/prevadzka/${shop.slug}" class="stop-propagation h-8 px-3 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-emerald-50 hover:text-emerald-600 transition-colors text-[10px] font-bold uppercase tracking-tight">
                            ${translations["Detail"] || 'Detail'}
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
    const translations = window.translations || {};
    const categories = Array.from(new Set(state.shops.map((shop) => shop.category).filter(Boolean)));
    categorySelect.innerHTML = `<option value="">${translations["All"] || 'Všetky'}</option>`;
    categories.forEach((category) => {
        const opt = document.createElement('option');
        opt.value = category;
        opt.textContent = category;
        categorySelect.appendChild(opt);
    });
}

function renderServices(shopId = null) {
    if (!servicesList) return;
    const translations = window.translations || {};

    let servicesToRender = state.services;
    if (shopId) {
        servicesToRender = state.services.filter((s) => String(s.profile_id) === String(shopId));
    }

    if (!servicesToRender.length) {
        servicesList.innerHTML = `<p class="text-sm text-slate-500">${translations["No services found for this business."] || 'Žiadne služby nenájdené pre túto prevádzku.'}</p>`;
        return;
    }

    servicesList.innerHTML = '';
    servicesToRender.forEach((service) => {
        const variants = state.variantsByService[service.id] || [];
        const hasVariants = variants.length > 0;
        const duration = service.base_duration_minutes ?? 30;
        const price = service.base_price ?? 0;
        const employeeNames =
            (service.employees || []).map((e) => e.name).join(', ') ||
            '';

        const card = document.createElement('div');
        card.className = 'service-card group !p-6 rounded-[32px] bg-white border border-slate-50 hover:border-emerald-100 transition-all shadow-sm hover:shadow-xl hover:shadow-emerald-200/20 flex flex-col gap-4';
        card.dataset.serviceCard = '1';
        card.dataset.shopId = service.profile_id;
        card.dataset.serviceId = service.id;

        let variantsHtml = '';
        if (hasVariants) {
            variantsHtml = `
                <div class="space-y-3 pt-4 border-t border-slate-50 mt-2">
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest ml-1">${translations["Available variants"] || 'Dostupné varianty'}</p>
                    <div class="grid gap-3">
                        ${variants.map(variant => `
                            <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:border-emerald-200 hover:bg-white transition-all group/variant">
                                <div class="flex-1">
                                    <p class="font-bold text-slate-900 group-hover/variant:text-emerald-600 transition-colors text-sm">${variant.name}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] text-slate-500">${variant.duration_minutes} min</span>
                                        <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                                        <span class="text-[10px] font-bold text-emerald-600">€${Number(variant.price ?? 0).toFixed(2)}</span>
                                    </div>
                                </div>
                                <button class="w-full sm:w-auto px-6 py-3 rounded-xl bg-emerald-500 text-white text-[10px] font-bold hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-200/50 flex items-center justify-center gap-2 flex-shrink-0" data-choose-variant="${variant.id}" data-service-id="${service.id}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    ${translations["Select"] || 'Vybrať termín'}
                                </button>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        card.innerHTML = `
            <div class="flex flex-col gap-1.5">
                <span class="inline-flex w-fit px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-widest leading-tight">${service.category ?? ''}</span>
                <p class="font-bold text-xl text-slate-900 group-hover:text-emerald-600 transition-colors leading-snug">${service.name}</p>
            </div>
            <div class="flex items-center gap-3 text-xs text-slate-500">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>${duration} min</span>
                </div>
                <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                <span class="font-bold text-emerald-600 text-sm">${hasVariants ? (translations['from'] || 'od') + ' ' : ''}€${Number(price).toFixed(2)}</span>
            </div>
            ${
                employeeNames
                    ? `<div class="mt-1 flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-100 w-fit overflow-hidden">
                         <div class="h-1.5 w-1.5 rounded-full bg-emerald-400 flex-shrink-0"></div>
                         <p class="text-[10px] font-bold text-slate-600 uppercase tracking-tight truncate">${translations["Employee"] || 'Zamestnanec'}: ${employeeNames}</p>
                       </div>`
                    : ''
            }
            ${!hasVariants ? `
                <button class="mt-2 px-4 py-4 w-full rounded-[20px] bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold transition shadow-lg shadow-emerald-200/50 flex items-center justify-center gap-2 group/btn" data-choose-service="${service.id}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span class="uppercase tracking-wide">${translations["Select"] || 'Vybrať termín'}</span>
                </button>
            ` : ''}
            ${variantsHtml}
        `;
        servicesList.appendChild(card);
    });

    // Event Listeners for Service buttons
    servicesList.querySelectorAll('[data-choose-service]').forEach((button) => {
        button.addEventListener('click', () => {
            const serviceId = button.dataset.chooseService;
            const service = state.services.find((s) => String(s.id) === String(serviceId));
            if (serviceSelect && serviceId) {
                if (shopSelect) {
                    shopSelect.value = service.profile_id;
                    populateServicesForShop(service.profile_id);
                }
                serviceSelect.value = serviceId;
                populateVariants(serviceId);
            }

            const bookingSection = document.getElementById('booking');
            if (bookingSection) {
                showBookingLoading();
                bookingSection.classList.remove('hidden');
                bookingSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // Event Listeners for Variant buttons
    servicesList.querySelectorAll('[data-choose-variant]').forEach((button) => {
        button.addEventListener('click', () => {
            const variantId = button.dataset.chooseVariant;
            const serviceId = button.dataset.serviceId;
            const service = state.services.find((s) => String(s.id) === String(serviceId));

            if (serviceSelect && serviceId) {
                if (shopSelect) {
                    shopSelect.value = service.profile_id;
                    populateServicesForShop(service.profile_id);
                }
                serviceSelect.value = serviceId;
                populateVariants(serviceId, variantId);
            }

            const bookingSection = document.getElementById('booking');
            if (bookingSection) {
                showBookingLoading();
                bookingSection.classList.remove('hidden');
                bookingSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

function populateShopSelect() {
    if (!shopSelect) return;
    const translations = window.translations || {};
    shopSelect.innerHTML = `<option value="">${translations["Choose business"] || 'Vyber prevádzku'}</option>`;
    state.shops.forEach((shop) => {
        const opt = document.createElement('option');
        opt.value = shop.id;
        opt.textContent = `${shop.name} — ${shop.city ?? ''}`;
        shopSelect.appendChild(opt);
    });
}

function populateServiceSelect() {
    if (!serviceSelect) return;
    const translations = window.translations || {};
    serviceSelect.innerHTML = `<option value="">${translations["Choose service"] || 'Vyber službu'}</option>`;
    state.services.forEach((service) => {
        const opt = document.createElement('option');
        opt.value = service.id;
        opt.textContent = `${service.name} (${service.shop_name ?? ''})`;
        serviceSelect.appendChild(opt);
    });
}

function populateServicesForShop(shopId) {
    if (!serviceSelect) return;
    const translations = window.translations || {};
    serviceSelect.innerHTML = `<option value="">${translations["Choose service"] || 'Vyber službu'}</option>`;
    state.services
        .filter((s) => String(s.profile_id) === String(shopId))
        .forEach((service) => {
            const opt = document.createElement('option');
            opt.value = service.id;
            opt.textContent = `${service.name} (${service.shop_name ?? ''})`;
            serviceSelect.appendChild(opt);
        });
}

function updatePakavozFieldsVisibility(serviceId) {
    if (!homePakavozFields || !homeEvcInput) return;

    const service = state.serviceById[serviceId];
    if (service && service.is_pakavoz_enabled) {
        homePakavozFields.classList.remove('hidden');
        homeEvcInput.setAttribute('required', 'required');
    } else {
        homePakavozFields.classList.add('hidden');
        homeEvcInput.removeAttribute('required');
    }
}

function populateVariants(serviceId, selectedVariantId = null) {
    if (!variantSelect) return;

    // Update Pakavoz fields visibility
    updatePakavozFieldsVisibility(serviceId);

    const translations = window.translations || {};
    const variants = state.variantsByService[serviceId] || [];
    variantSelect.innerHTML = `<option value="">${translations["None (use service base)"] || 'Žiadny (použiť základ služby)'}</option>`;
    variants.forEach((variant) => {
        const opt = document.createElement('option');
        opt.value = variant.id;
        opt.textContent = `${variant.name} — ${variant.duration_minutes} min (€${Number(variant.price ?? 0).toFixed(2)})`;
        variantSelect.appendChild(opt);
    });

    if (selectedVariantId) {
        variantSelect.value = selectedVariantId;
        assignEmployeeForVariant(selectedVariantId);
        fetchAvailability(true);
    } else if (variants[0]) {
        variantSelect.value = variants[0].id;
        assignEmployeeForVariant(variants[0].id);
        fetchAvailability(true);
    } else {
        variantSelect.value = '';
        assignEmployeeForService(serviceId);
        fetchAvailability(true);
        return;
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
    const translations = window.translations || {};
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
        populateShopSelect();
        populateServiceSelect();
        populateCategorySelect();
        renderServices();
    } catch (error) {
        console.error('Nepodarilo sa načítať prevádzky', error);
        if (shopList) {
            shopList.innerHTML = `<p class="text-sm text-red-500">${translations["Failed to load businesses."] || 'Nepodarilo sa načítať prevádzky.'}</p>`;
        }
    }
}

async function fetchWeekAvailability() {
    if (!shopSelect?.value || !serviceSelect?.value || !state.calendarStart) {
        return;
    }

    showBookingLoading();
    const startIso = formatIsoDate(state.calendarStart);

    try {
        const response = await axios.post('/api/availability', {
            profile_id: shopSelect.value,
            service_id: serviceSelect.value,
            service_variant_id: variantSelect.value || null,
            employee_id: employeeInput?.value || null,
            date: startIso,
            days: 35,
        });

        if (response.data?.closed_days) {
            state.closedDays = response.data.closed_days;
            renderCalendar();

            // Prekreslíme Flatpickr ak existuje
            document.querySelectorAll('input').forEach(el => {
                if (el._flatpickr) el._flatpickr.redraw();
            });
        }
    } catch (error) {
        console.error('Chyba pri načítaní týždennej dostupnosti', error);
    } finally {
        hideBookingLoading();
    }
}

async function fetchAvailability(autoSelectNearest = false) {
    const translations = window.translations || {};
    if (!shopSelect?.value || !serviceSelect?.value) {
        resetSlots(translations["Choose business and service."] || 'Vyber prevádzku a službu.');
        return;
    }

    showBookingLoading();
    let date = dateInput?.value || formatIsoDate(new Date());

    try {
        const response = await axios.post('/api/availability', {
            profile_id: shopSelect.value,
            service_id: serviceSelect.value,
            service_variant_id: variantSelect.value || null,
            employee_id: employeeInput?.value || null,
            date,
            days: autoSelectNearest ? 35 : 1,
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

        // Prekreslíme Flatpickr ak existuje
        document.querySelectorAll('input').forEach(el => {
            if (el._flatpickr) el._flatpickr.redraw();
        });
    } catch (error) {
        const translations = window.translations || {};
        console.error('Chyba pri načítaní dostupnosti', error);
        resetSlots(translations["Failed to load availability."] || 'Nepodarilo sa načítať dostupnosť.');
    } finally {
        hideBookingLoading();
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
        const translations = window.translations || {};
        if (lockTimeout) clearTimeout(lockTimeout);
        if (refreshTimeout) clearTimeout(refreshTimeout);

        // 4 minutes and 50 seconds = 290000 ms (10 seconds before 5 minutes)
        lockTimeout = setTimeout(() => {
            Swal.fire({
                title: translations["Continue booking?"] || 'Pokračovať v rezervácii?',
                text: translations["Due to inactivity, your pending reservation will be cancelled soon."] || 'Z dôvodu neaktivity bude vaša rozpracovaná rezervácia čoskoro zrušená.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#f43f5e',
                confirmButtonText: translations["Continue"] || 'Pokračovať',
                cancelButtonText: translations["Cancel"] || 'Zrušiť',
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
        const translations = window.translations || {};
        if (!slots.length) {
            resetSlots(translations["No free slots for selected day."] || 'Žiadne voľné termíny pre vybraný deň.');
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

                const translations = window.translations || {};
                if (isLocking) {
                    button.disabled = true;
                    button.dataset.status = 'busy';
                    button.classList.add('opacity-70', 'cursor-not-allowed');
                    statusP.classList.replace('text-emerald-600', 'text-orange-500');
                    statusP.textContent = translations["locking"] || 'obsadzuje sa';
                    button.title = translations["Someone else is currently filling out a reservation"] || 'Niekto iný práve vypĺňa rezerváciu';
                } else if (!isAvailable) {
                    button.disabled = true;
                    button.dataset.status = 'busy';
                    button.classList.add('opacity-60', 'cursor-not-allowed');
                    statusP.classList.replace('text-emerald-600', 'text-slate-500');
                    statusP.textContent = translations["busy"] || 'obsadené';
                    button.title = translations["busy"] || 'Obsadené';
                } else {
                    statusP.textContent = translations["free"] || 'voľný';
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
                            .then((response) => {
                                state.lockToken = response.data.token;
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
            renderGroup(translations["Morning"] || 'Dopoludnie', morning);
        }
        if (afternoon.length) {
            renderGroup(translations["Afternoon"] || 'Popoludnie', afternoon);
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

    if (state.lockToken) {
        payload.lock_token = state.lockToken;
    }

    const translations = window.translations || {};
    if (!payload.start_at) {
        bookingOutput.textContent = translations["Select time to lock slot."] || 'Vyber čas, aby sme zamkli slot.';
        return;
    }

    bookingOutput.textContent = translations["Checking slot..."] || `Overujem slot...`;

    const selectedVariant = state.variantMap[payload.service_variant_id];
    const selectedService =
        state.serviceById[selectedVariant?.service_id] ||
        state.services.find((s) => String(s.id) === String(payload.service_id));
    const durationMinutes = selectedVariant ? (selectedVariant.duration_minutes ?? selectedService?.base_duration_minutes) : (selectedService?.base_duration_minutes || 30);
    const price = selectedVariant ? (selectedVariant.price ?? selectedService?.base_price) : (selectedService?.base_price ?? 0);
    let endTimeReadable = '';
    try {
        const startDate = new Date(payload.start_at);
        const endDate = new Date(startDate.getTime() + durationMinutes * 60000);
        endTimeReadable = `${window.timeFormatter.format(startDate)} - ${window.timeFormatter.format(endDate)}`;
    } catch (e) {
        endTimeReadable = '';
    }

    axios
        .post('/api/appointments', payload)
        .then((response) => {
            const translations = window.translations || {};
            const appointment = response.data;
            const successMessage = `${translations["Appointment confirmed:"] || 'Termín potvrdený:'} ${appointment.service?.name ?? 'Služba'} ${window.dateTimeFormatter.format(
                new Date(appointment.start_at),
            )} ${endTimeReadable ? `(${endTimeReadable})` : ''}, ${translations["price"] || 'cena'} €${Number(price).toFixed(2)}.`;

            bookingOutput.textContent = successMessage;

            if (typeof Swal !== 'undefined') {
                const translations = window.translations || {};
                const title = (appointment.service?.name ?? 'Služba').replace(/'/g, "\\'");
                const start = appointment.start_at;
                const shopName = (state.shops.find(s => String(s.id) === String(payload.profile_id))?.name ?? 'BookMe').replace(/'/g, "\\'");

                Swal.fire({
                    title: translations["Booking successful!"] || 'Rezervácia úspešná!',
                    html: `
                        <p class="mb-4">${successMessage}</p>
                        <div class="flex flex-col gap-2 mt-4">
                            <button onclick="downloadIcs('${title}', '${start}', ${durationMinutes}, '${shopName}')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-900 font-semibold transition flex items-center justify-center gap-2">
                                📱 ${translations["Add to iOS calendar"] || 'Pridať do iOS kalendára'}
                            </button>
                            <button onclick="openGoogleCalendar('${title}', '${start}', ${durationMinutes}, '${shopName}')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-900 font-semibold transition flex items-center justify-center gap-2">
                                🤖 ${translations["Add to Android calendar"] || 'Pridať do Android kalendára'}
                            </button>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonColor: '#10b981',
                    confirmButtonText: translations["Close"] || 'Zavrieť'
                }).then(() => {
                    // Reset formy
                    if (bookingForm) {
                        bookingForm.reset();
                        bookingOutput.textContent = translations["Choose service and time, the system will check availability and confirm your appointment."] || 'Vyber službu a čas, systém preverí dostupnosť a potvrdí ti termín.';
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
            const translations = window.translations || {};
            console.error('Chyba pri rezervácii', error);
            const errors = error.response?.data?.errors;
            let message = error.response?.data?.message || (translations["Error booking appointment."] || 'Nepodarilo sa vytvoriť rezerváciu.');
            if (errors) {
                message = Object.values(errors).flat().join(' ');
            }
            bookingOutput.textContent = message;
        });
});

shopSelect?.addEventListener('change', () => {
    const shopId = shopSelect.value;
    const translations = window.translations || {};
    if (shopId) {
        populateServicesForShop(shopId);
        fetchWeekAvailability();
    } else {
        populateServiceSelect();
    }
    variantSelect && (variantSelect.innerHTML = `<option value="">${translations["Choose variant"] || 'Vyber variant'}</option>`);
    resetSlots(translations["Choose variant."] || 'Vyber variant.');
    updatePakavozFieldsVisibility(null);
});

serviceSelect?.addEventListener('change', () => {
    const translations = window.translations || {};
    const serviceId = serviceSelect.value;
    if (serviceId) {
        populateVariants(serviceId);
        fetchWeekAvailability();
    } else {
        variantSelect && (variantSelect.innerHTML = `<option value="">${translations["Choose variant"] || 'Vyber variant'}</option>`);
        resetSlots(translations["Choose service."] || 'Vyber službu.');
        updatePakavozFieldsVisibility(null);
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

// Header & Scroll Logic
const scrollToTopBtn = document.getElementById('scroll-to-top');
const headerWrapper = document.getElementById('header-wrapper');
const mainHeader = document.getElementById('main-header');
let lastScrollY = window.scrollY;

window.addEventListener('scroll', () => {
    const currentScrollY = window.scrollY;

    // Scroll to Top visibility
    if (scrollToTopBtn) {
        if (currentScrollY > 300) {
            scrollToTopBtn.classList.remove('opacity-0', 'invisible');
            scrollToTopBtn.classList.add('opacity-100', 'visible');
        } else {
            scrollToTopBtn.classList.add('opacity-0', 'invisible');
            scrollToTopBtn.classList.remove('opacity-100', 'visible');
        }
    }

    // Sticky Header Logic
    if (headerWrapper && mainHeader) {
        // Hide on scroll down, show on scroll up
        if (currentScrollY > lastScrollY && currentScrollY > 200) {
            // Scrolling down
            headerWrapper.classList.replace('translate-y-0', '-translate-y-full');
        } else {
            // Scrolling up
            headerWrapper.classList.replace('-translate-y-full', 'translate-y-0');

            if (currentScrollY > 20 || window.innerWidth < 768) {
                headerWrapper.classList.add('is-scrolled');
                mainHeader.classList.add('!py-3');
                mainHeader.classList.remove('md:py-6');
            } else {
                headerWrapper.classList.remove('is-scrolled');
                mainHeader.classList.remove('!py-3');
                mainHeader.classList.add('md:py-6');
            }
        }
    }

    lastScrollY = currentScrollY;
}, { passive: true });

if (scrollToTopBtn) {
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
    const monthFormatter = new Intl.DateTimeFormat(window.locale || 'sk-SK', { month: 'long', year: 'numeric', timeZone: TIME_ZONE });
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

        const escapeICS = (str) => {
            if (!str) return '';
            return str.replace(/[\\,;]/g, (match) => '\\' + match)
                      .replace(/\n/g, '\\n');
        };

        const icsMsg = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//BookMe//SK',
            'CALSCALE:GREGORIAN',
            'BEGIN:VEVENT',
            `DTSTART:${format(startDate)}`,
            `DTEND:${format(endDate)}`,
            `SUMMARY:${escapeICS(title)}`,
            `DESCRIPTION:${escapeICS((window.translations?.["Reservation at"] || 'Rezervácia v ') + shopName)}`,
            `LOCATION:${escapeICS(shopName)}`,
            'END:VEVENT',
            'END:VCALENDAR'
        ].join('\r\n');

        const blob = new Blob([icsMsg], { type: 'text/calendar;charset=utf-8' });
        const translations = window.translations || {};
        const link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.setAttribute('download', translations["reservation.ics"] || 'reservation.ics');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    window.openGoogleCalendar = function(title, start, duration, shopName) {
        const translations = window.translations || {};
        const startDate = new Date(start);
        const endDate = new Date(startDate.getTime() + duration * 60000);

        const format = (d) => d.toISOString().replace(/-|:|\.\d+/g, '');

        const url = `https://www.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(title)}&dates=${format(startDate)}/${format(endDate)}&details=${encodeURIComponent((translations["Reservation at"] || 'Rezervácia v ') + shopName)}&location=${encodeURIComponent(shopName)}`;
        window.open(url, '_blank');
    };

    // Flatpickr initialization
    function initFlatpickr() {
        if (typeof flatpickr === 'undefined') return;

        const config = {
            dateFormat: "Y-m-d",
            locale: window.locale || "sk",
            rangeSeparator: " to ",
            disableMobile: true, // Zablokuje natívny kalendár na mobiloch
            allowInput: true,
            onReady: function(selectedDates, dateStr, instance) {
                // Pre iOS/Android zabezpečíme, že pole bude readonly pre natívnu klávesnicu,
                // ale flatpickr ho bude môcť ovládať.
                instance.element.setAttribute('readonly', 'readonly');
            },
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                const isoDate = formatIsoDate(dayElem.dateObj);
                const closedDays = (window.adminState ? window.adminState.closedDays : state.closedDays) || [];
                if (closedDays.includes(isoDate)) {
                    dayElem.classList.add('is-closed-day');
                }
            }
        };

        const dateInputs = document.querySelectorAll('input[name="date"]:not([type="hidden"]), input[type="date"]');
        dateInputs.forEach(el => {
            // Ak už je flatpickr inicializovaný, alebo ide o interný element kalendára, preskočíme
            if (el._flatpickr || el.closest('.flatpickr-calendar')) return;

            // Ak má element špecifický onchange z dashboardu (napr. quick booking)
            const originalOnchange = el.onchange;

            const instanceConfig = { ...config };

            // Zakážeme minulosť pre rezervácie (pokiaľ nie je explicitne povolená cez data-allow-past)
            if (el.dataset.allowPast === undefined) {
                instanceConfig.minDate = "today";
            }

            if (el.dataset.mode !== undefined) {
                instanceConfig.mode = el.dataset.mode;
            }

            // Špecifická logika pre domovskú stránku a výber dátumu
            if (el.dataset.dateInput !== undefined) {
                instanceConfig.onSelect = function(selectedDates) {
                    if (selectedDates.length > 0) {
                        setSelectedDate(selectedDates[0]);
                    }
                };
                // Flatpickr používa onChange namiesto onSelect
                instanceConfig.onChange = function(selectedDates) {
                    if (selectedDates.length > 0) {
                        setSelectedDate(selectedDates[0]);
                    }
                };
            }

            if (originalOnchange) {
                const prevOnChange = instanceConfig.onChange;
                instanceConfig.onChange = function(selectedDates, dateStr, instance) {
                    if (prevOnChange) prevOnChange(selectedDates, dateStr, instance);
                    el.dispatchEvent(new Event('change'));
                };
            }

            // Zmena typu z date na text, aby sme predišli natívnemu kalendáru
            if (el.type === 'date') {
                el.type = 'text';
            }

            flatpickr(el, instanceConfig);
        });

        // Time inputs
        const timeInputs = document.querySelectorAll('input[type="time"], input.js-flatpickr-time');
        timeInputs.forEach(el => {
            if (el._flatpickr || el.classList.contains('numInput') || el.closest('.flatpickr-calendar')) return;

            // Zmena typu na text, aby sme predišli natívnemu picker-u
            if (el.type === 'time') {
                el.type = 'text';
            }

            flatpickr(el, {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                locale: window.locale || "sk",
                disableMobile: true,
                onReady: function(selectedDates, dateStr, instance) {
                    instance.element.setAttribute('readonly', 'readonly');
                }
            });
        });
    }

    initFlatpickr();

    // Re-inicializácia po dynamických zmenách ak sú potrebné
    window.reinitFlatpickr = initFlatpickr;
}

// ...existing initializers
initSelectedDate();
renderCalendar();
bindCalendarNav();
initAdminDashboard();

window.confirmDeleteAppointment = function(event, form) {
    if (event) event.preventDefault();
    const translations = window.translations || {};

    Swal.fire({
        title: translations['Are you sure?'] || 'Ste si istý?',
        text: translations['Are you sure you want to delete this appointment?'] || 'Skutočne chcete odstrániť túto rezerváciu?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: translations['Yes, delete it!'] || 'Áno, odstrániť!',
        cancelButtonText: translations['Cancel'] || 'Zrušiť',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const selectedDateInput = document.getElementById('admin-selected-date');
            if (selectedDateInput && selectedDateInput.value) {
                const action = form.getAttribute('action');
                const separator = action.includes('?') ? '&' : '?';
                form.setAttribute('action', action + separator + 'date=' + selectedDateInput.value);
            }
            form.submit();
        }
    });
};

function initAdminDashboard() {
    const adminCalGrid = document.querySelector('[data-admin-cal-grid]');
    const adminCalMonth = document.querySelector('[data-admin-cal-month]');
    const adminCalPrev = document.querySelector('[data-admin-cal-prev]');
    const adminCalNext = document.querySelector('[data-admin-cal-next]');
    const adminDateInput = document.getElementById('admin-selected-date');
    const appointmentsList = document.getElementById('appointments-list');
    const upcomingTitle = document.getElementById('upcoming-title');

    if (!adminCalGrid || !adminDateInput) return;

    // Inicializácia z URL parametra ak existuje
    const urlParams = new URLSearchParams(window.location.search);
    const dateParam = urlParams.get('date');
    let initialDate = new Date();

    if (dateParam && /^\d{4}-\d{2}-\d{2}$/.test(dateParam)) {
        initialDate = new Date(dateParam);
    }

    window.adminState.calendarStart = startOfWeek(initialDate);
    window.adminState.selectedDate = formatIsoDate(initialDate);
    adminDateInput.value = window.adminState.selectedDate;

    if (window.adminState.closedDays.length === 0 && window.adminInitialClosedDays) {
        window.adminState.closedDays = window.adminInitialClosedDays;
    }

    async function fetchAdminCalendarStatus() {
        const startIso = formatIsoDate(window.adminState.calendarStart);
        try {
            const response = await axios.get(`/owner/appointments/calendar-status?start=${startIso}&days=35`);
            window.adminState.closedDays = response.data.closed_days || [];
            renderAdminCalendar();

            // Prekreslíme Flatpickr ak existuje, aby sa aplikovali nové closedDays
            document.querySelectorAll('input').forEach(el => {
                if (el._flatpickr) el._flatpickr.redraw();
            });
        } catch (error) {
            console.error('Chyba pri načítaní stavu kalendára', error);
        }
    }

    function renderAdminCalendar() {
        if (!adminCalGrid || !adminCalMonth) return;
        const start = window.adminState.calendarStart;
        const locale = window.locale || 'sk-SK';
        const monthFormatter = new Intl.DateTimeFormat(locale, { month: 'long', year: 'numeric', timeZone: TIME_ZONE });
        adminCalMonth.textContent = monthFormatter.format(start);

        adminCalGrid.querySelectorAll('.calendar-item').forEach((el) => el.remove());

        for (let i = 0; i < 7; i++) {
            const day = new Date(start);
            day.setDate(start.getDate() + i);
            const dayNum = day.getDate();
            const iso = formatIsoDate(day);
            const isClosed = window.adminState.closedDays.includes(iso);

            const item = document.createElement('a');
            item.href = '#';
            item.className = 'calendar-item overflow-hidden';
            if (isClosed) {
                item.classList.add('!text-rose-500', '!bg-rose-50', 'hover:!bg-rose-100');
            }
            item.textContent = dayNum;

            if (window.adminState.selectedDate === iso) {
                item.classList.add('active');
            }

            item.addEventListener('click', (e) => {
                e.preventDefault();
                window.adminState.selectedDate = iso;
                adminDateInput.value = iso;
                fetchAdminAppointments(iso);
                renderAdminCalendar();
            });

            adminCalGrid.appendChild(item);
        }
    }

    async function fetchAdminAppointments(date) {
        if (!appointmentsList) return;

        const translations = window.translations || {};
        const revenueElement = document.getElementById('revenue-today-value');
        const appointmentsStatCount = document.getElementById('appointments-stat-count');
        const appointmentsStatLabel = document.getElementById('appointments-stat-label');
        const freeSlotsStatCount = document.getElementById('free-slots-stat-count');
        const freeSlotsStatLabel = document.getElementById('free-slots-stat-label');

        // Loading animation
        appointmentsList.innerHTML = `
            <div class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-500"></div>
            </div>
        `;

        if (revenueElement) revenueElement.classList.add('opacity-50');
        if (appointmentsStatCount) appointmentsStatCount.classList.add('opacity-50');
        if (freeSlotsStatCount) freeSlotsStatCount.classList.add('opacity-50');

        // Predbežný vizuálny feedback
        const d = new Date(date);
        const dayNames = [
            translations["Sunday"] || 'Nedeľa',
            translations["Monday"] || 'Pondelok',
            translations["Tuesday"] || 'Utorok',
            translations["Wednesday"] || 'Streda',
            translations["Thursday"] || 'Štvrtok',
            translations["Friday"] || 'Piatok',
            translations["Saturday"] || 'Sobota'
        ];

        const isToday = d.toDateString() === new Date().toDateString();
        const dayName = dayNames[d.getDay()];

        // Aktualizácia štítkov v štatistikách
        if (appointmentsStatLabel) {
            appointmentsStatLabel.textContent = isToday
                ? (translations["Appointments today"] || 'Rezervácií na dnes')
                : (translations["Appointments for"] || 'Rezervácií na') + ' ' + dayName;
        }
        if (freeSlotsStatLabel) {
            freeSlotsStatLabel.textContent = isToday
                ? (translations["Free slots for today"] || 'Voľné termíny na dnes')
                : (translations["Free slots for"] || 'Voľné termíny na') + ' ' + dayName;
        }

        const formattedTitle = isToday
            ? (translations["Appointments for today"] || 'Rezervácií na dnes')
            : `${translations["Appointments for"] || 'Rezervácií na'} ${dayName} ${d.getDate()}.${d.getMonth()+1}.${d.getFullYear()}`;

        if (upcomingTitle) upcomingTitle.textContent = formattedTitle;

        try {
            const response = await axios.get(`/owner/appointments/day?date=${date}`);
            const data = response.data;
            const appointments = data.appointments;

            if (revenueElement) {
                revenueElement.textContent = data.revenue_formatted;
                revenueElement.classList.remove('opacity-50');
            }
            if (appointmentsStatCount) {
                appointmentsStatCount.textContent = data.appointments_count;
                appointmentsStatCount.classList.remove('opacity-50');
            }
            if (freeSlotsStatCount) {
                freeSlotsStatCount.textContent = data.free_slots_count;
                freeSlotsStatCount.classList.remove('opacity-50');
            }

            if (appointments.length === 0) {
                appointmentsList.innerHTML = `
                    <div class="py-8 text-center" id="no-appointments">
                        <p class="text-sm text-slate-500 italic">${translations["No upcoming appointments for this day."] || 'Žiadne nadchádzajúce rezervácie na tento deň.'}</p>
                    </div>`;
                return;
            }

            appointmentsList.innerHTML = appointments.map(a => `
                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 hover:bg-slate-50/50 transition-colors ${a.status === 'completed' ? 'opacity-50' : ''}">
                    <div class="flex items-center gap-4">
                        <div class="text-center min-w-[50px] px-2 py-1 rounded-lg ${a.status === 'completed' ? 'bg-slate-100 text-slate-500' : 'bg-emerald-50 text-emerald-700'}">
                            <p class="text-lg font-bold leading-tight">${a.start_time}</p>
                        </div>
                        <div>
                            <p class="font-bold ${a.status === 'completed' ? 'text-slate-500' : 'text-slate-900'} leading-tight">${a.service_name}</p>
                            <p class="text-xs text-slate-500">${a.customer_name} • ${a.customer_phone || ''}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        ${a.status !== 'confirmed' || (a.requires_confirmation ?? true) ? `
                            <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-tight
                                ${a.status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' :
                                   (a.status === 'pending' ? 'bg-orange-100 text-orange-700' :
                                   (a.status === 'completed' ? 'bg-slate-100 text-slate-500' : 'bg-slate-100 text-slate-500'))}">
                                ${a.status === 'completed' ? (translations["Completed"] || 'Completed') : (a.status === 'confirmed' ? (translations["Confirmed"] || 'Confirmed') : (a.status === 'pending' ? (translations["Pending"] || 'Pending') : a.status))}
                            </span>
                        ` : ''}
                        <div class="flex items-center gap-1">
                            ${a.status === 'pending' ? `
                                <form method="POST" action="${a.confirm_url}">
                                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                    <button class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition" title="${translations["Confirm"] || 'Confirm'}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                            ` : ''}

                            ${a.status !== 'completed' && a.status !== 'cancelled' ? `
                                <form method="POST" action="${a.status_update_url}">
                                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                    <input type="hidden" name="status" value="completed">
                                    <button class="px-3 py-1.5 rounded-lg bg-emerald-500 text-white text-xs font-bold hover:bg-emerald-600 transition shadow-sm" title="${translations["Mark as completed"] || 'Mark as completed'}">
                                        ${translations["Completed"] || 'Completed'}
                                    </button>
                                </form>
                            ` : ''}

                            <button onclick='openEditAppointmentModal(${JSON.stringify({
                                id: a.id,
                                customer_name: a.customer_name,
                                customer_phone: a.customer_phone,
                                service_name: a.service_name,
                                date: a.date_raw,
                                start_time: a.start_time,
                                duration_minutes: a.duration_minutes,
                                price: a.price,
                                employee_id: a.employee_id,
                                notes: ''
                            })})' class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 text-xs font-bold hover:bg-emerald-100 transition" title="${translations["Edit"] || 'Edit'}">
                                ${translations["Edit"] || 'Edit'}
                            </button>

                            <button onclick='openEditAppointmentModal(${JSON.stringify({
                                id: a.id,
                                customer_name: a.customer_name,
                                customer_phone: a.customer_phone,
                                service_name: a.service_name,
                                date: a.date_raw,
                                start_time: a.start_time,
                                duration_minutes: a.duration_minutes,
                                price: a.price,
                                employee_id: a.employee_id,
                                notes: ''
                            })})' class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200 transition" title="${translations["Reschedule"] || 'Reschedule'}">
                                ${translations["Reschedule"] || 'Reschedule'}
                            </button>

                            <form method="POST" action="${a.delete_url}" onsubmit="confirmDeleteAppointment(event, this)">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold hover:bg-rose-100 transition" title="${translations["Delete"] || 'Delete'}">
                                    ${translations["Delete"] || 'Delete'}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Chyba pri načítaní termínov', error);
            appointmentsList.innerHTML = `<div class="py-8 text-center text-red-500">${translations["Failed to load appointments."] || 'Nepodarilo sa načítať termíny.'}</div>`;
        }
    }

    if (adminCalPrev) {
        adminCalPrev.addEventListener('click', (e) => {
            e.preventDefault();
            const newDate = new Date(adminState.calendarStart);
            newDate.setDate(newDate.getDate() - 7);

            const today = new Date();
            const currentWeekMonday = new Date(today.getFullYear(), today.getMonth(), today.getDate());
            const day = (currentWeekMonday.getDay() + 6) % 7;
            currentWeekMonday.setDate(currentWeekMonday.getDate() - day);
            currentWeekMonday.setHours(0, 0, 0, 0);

            if (newDate < currentWeekMonday) return;

            adminState.calendarStart = newDate;
            fetchAdminCalendarStatus();
        });
    }

    if (adminCalNext) {
        adminCalNext.addEventListener('click', (e) => {
            e.preventDefault();
            const newDate = new Date(adminState.calendarStart);
            newDate.setDate(newDate.getDate() + 7);
            adminState.calendarStart = newDate;
            fetchAdminCalendarStatus();
        });
    }

    fetchAdminCalendarStatus();
}

// Inicializácia najbližších termínov pre služby
if (typeof window !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => window.loadServicesNextSlots());
    } else {
        window.loadServicesNextSlots();
    }
}
