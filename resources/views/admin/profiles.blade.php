@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="font-display text-3xl text-slate-900">{{ __('Manage businesses') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Overview and management of registered businesses in the system.') }}</p>
        </div>
        <button onclick="openAddModal()" class="px-4 py-2 rounded-xl bg-emerald-500 text-white text-sm font-bold hover:bg-emerald-600 transition shadow-lg shadow-emerald-200">
            + {{ __('Add business') }}
        </button>
    </div>


    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('admin.profiles') }}" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ !$plan ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-100' }}">
            {{ __('All') }}
        </a>
        <a href="{{ route('admin.profiles', ['plan' => 'premium']) }}" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ $plan === 'premium' ? 'bg-amber-500 text-white shadow-lg' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-100' }}">
            Premium
        </a>
        <a href="{{ route('admin.profiles', ['plan' => 'basic']) }}" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ $plan === 'basic' ? 'bg-blue-500 text-white shadow-lg' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-100' }}">
            Basic
        </a>
        <a href="{{ route('admin.profiles', ['plan' => 'free']) }}" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ $plan === 'free' ? 'bg-emerald-500 text-white shadow-lg' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-100' }}">
            Trial
        </a>
    </div>

    <div class="card space-y-3">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-lg text-slate-900">{{ __('Business list') }}</h2>
            <div class="relative flex-1 max-w-xs">
                <input type="text" id="profile-search" class="input-control !py-2 !text-sm pl-10" placeholder="{{ __('Search business...') }}">
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        <div class="space-y-2 max-h-[75vh] overflow-y-auto pr-1" id="profiles-list">
            @forelse($profiles as $profile)
                <div class="border border-slate-100 rounded-xl p-3 bg-white/80 flex items-center justify-between gap-3 profile-item"
                     data-name="{{ strtolower($profile->name) }}"
                     data-city="{{ strtolower($profile->city) }}"
                     data-category="{{ strtolower($profile->category) }}">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 flex-shrink-0 rounded-lg bg-slate-50 border border-slate-100 overflow-hidden flex items-center justify-center">
                            @if($profile->logo_url)
                                <img src="{{ $profile->logo_url }}" alt="{{ $profile->name }} logo" class="w-full h-full object-contain">
                            @else
                                <span class="text-slate-300 font-bold text-lg">{{ substr($profile->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $profile->name }}</p>
                            <p class="text-xs text-slate-500">{{ $profile->category }} • {{ $profile->city }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    {{ $profile->status === 'published' ? 'bg-emerald-100 text-emerald-700' :
                                       ($profile->status === 'pending' ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-600') }}">
                                    {{ $profile->status }}
                                </span>
                                <span class="text-[10px] text-slate-400">{{ $profile->owner ? $profile->owner->email : '' }}</span>
                            </div>
                            @if($profile->subscription_starts_at)
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="text-[10px] font-bold uppercase tracking-tight text-slate-400">{{ __('Free version') }}:</span>
                                    @if($profile->trial_days_left > 0)
                                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">{{ __('Remaining') }} {{ $profile->trial_time_left }}</span>
                                    @else
                                        <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md">{{ __('Expired') }} ({{ $profile->trial_ends_at->format('d.m.Y') }})</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex gap-1">
                        @if($profile->status === 'pending')
                            <form action="{{ route('admin.profiles.publish', $profile) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-500 text-white text-xs font-bold hover:bg-emerald-600 transition shadow-sm shadow-emerald-200">
                                    {{ __('Publish') }}
                                </button>
                            </form>
                        @endif
                        <button type="button"
                                onclick="openEditModal({{ json_encode($profile) }})"
                                class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition">
                                {{ __('Edit') }}
                        </button>
                        <form action="{{ route('admin.profiles.delete', $profile) }}" method="POST" onsubmit="return confirmDelete(event, '{{ __('Are you sure you want to delete this business?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg bg-slate-100 text-slate-400 hover:bg-red-50 hover:text-red-500 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">{{ __('No businesses.') }}</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="add-modal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeAddModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-100">
            <div class="bg-white p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-900" id="modal-title">{{ __('Add business') }}</h3>
                    <button onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l18 18"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.profiles.store') }}" class="space-y-3" enctype="multipart/form-data">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="label">{{ __('Business name') }}</label>
                            <input type="text" name="name" class="input-control" value="{{ old('name') }}" required>
                        </div>
                        <div>
                            <label class="label">{{ __('Slug') }}</label>
                            <input type="text" name="slug" class="input-control" value="{{ old('slug') }}" placeholder="{{ __('automatic by name') }}">
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="label">{{ __('Email (owner login)') }}</label>
                            <input type="email" name="email" class="input-control" value="{{ old('email') }}" placeholder="info@prevadzka.sk" required>
                        </div>
                        <div>
                            <label class="label">{{ __('Owner password') }}</label>
                            <input type="password" name="password" class="input-control" placeholder="{{ __('Min. 8 characters') }}" required>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="label">{{ __('Category') }}</label>
                            <input type="text" name="category" class="input-control" value="{{ old('category') }}" placeholder="{{ __('Hairdressing') }}">
                        </div>
                        <div>
                            <label class="label">{{ __('City') }}</label>
                            <input type="text" name="city" class="input-control" value="{{ old('city') }}" placeholder="{{ __('City') }}">
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="label">Adresa</label>
                            <input type="text" name="address_line1" class="input-control" value="{{ old('address_line1') }}" placeholder="Ulica 123">
                        </div>
                        <div>
                            <label class="label">Timezone</label>
                            <input type="text" name="timezone" class="input-control" value="{{ old('timezone', 'Europe/Bratislava') }}">
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-1 gap-3">
                        <div>
                            <label class="label">Popis prevádzky</label>
                            <textarea name="description" class="input-control" rows="3" placeholder="Krátky popis prevádzky, ktorý uvidia klienti...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="label">Logo prevádzky</label>
                            <input type="file" name="logo" class="input-control !p-2 text-xs" onchange="previewImage(this, 'add-logo-preview-modal')">
                            <div id="add-logo-preview-modal" class="mt-2 hidden">
                                <img src="" class="h-12 w-12 object-contain rounded-lg border border-slate-200" alt="Logo preview">
                            </div>
                        </div>
                        <div>
                            <label class="label">Banner prevádzky</label>
                            <input type="file" name="banner" class="input-control !p-2 text-xs" onchange="previewImage(this, 'add-banner-preview-modal')">
                            <div id="add-banner-preview-modal" class="mt-2 hidden">
                                <img src="" class="h-12 w-24 object-cover rounded-lg border border-slate-200" alt="Banner preview">
                            </div>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-1 gap-3">
                        <div>
                            <label class="label">Telefón (voliteľné)</label>
                            <input type="text" name="phone" class="input-control" value="{{ old('phone') }}" placeholder="+421...">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 transition">Zrušiť</button>
                        <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow-lg shadow-emerald-200">
                            Uložiť prevádzku
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeEditModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-100">
            <div class="bg-white p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-900" id="modal-title">{{ __('Edit business') }}</h3>
                    <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l18 18"/></svg>
                    </button>
                </div>

                <form id="edit-form" method="POST" action="" class="space-y-4" enctype="multipart/form-data">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="label">{{ __('Business name') }}</label>
                            <input type="text" name="name" id="edit-name" class="input-control" required>
                        </div>
                        <div>
                            <label class="label">{{ __('Slug') }}</label>
                            <input type="text" name="slug" id="edit-slug" class="input-control" required>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="label">{{ __('Category') }}</label>
                            <input type="text" name="category" id="edit-category" class="input-control">
                        </div>
                        <div>
                            <label class="label">{{ __('Status') }}</label>
                            <select name="status" id="edit-status" class="input-control" required>
                                <option value="draft">Draft</option>
                                <option value="pending">{{ __('Pending') }}</option>
                                <option value="published">{{ __('Published') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="label">Mesto</label>
                            <input type="text" name="city" id="edit-city" class="input-control">
                        </div>
                        <div>
                            <label class="label">Adresa</label>
                            <input type="text" name="address_line1" id="edit-address" class="input-control">
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="label">Email</label>
                            <input type="email" name="email" id="edit-email" class="input-control">
                        </div>
                        <div>
                            <label class="label">Telefón</label>
                            <input type="text" name="phone" id="edit-phone" class="input-control">
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="label">Timezone</label>
                            <input type="text" name="timezone" id="edit-timezone" class="input-control">
                        </div>
                        <div>
                            <label class="label">Majiteľ (Owner)</label>
                            <select name="owner_id" id="edit-owner_id" class="input-control" required>
                                @foreach($owners as $owner)
                                    <option value="{{ $owner->id }}">{{ $owner->name }} ({{ $owner->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="label">Dátum spustenia predplatného</label>
                            <input type="date" name="subscription_starts_at" id="edit-subscription_starts_at" class="input-control">
                        </div>
                        <div>
                            <label class="label">Plán predplatného</label>
                            <select name="subscription_plan" id="edit-subscription_plan" class="input-control" required>
                                <option value="free">Bezplatná verzia (Trial)</option>
                                <option value="basic">Základný (20 €/mesiac)</option>
                                <option value="premium">Premium</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="label">Logo prevádzky</label>
                            <input type="file" name="logo" class="input-control !p-2 text-xs" onchange="previewImage(this, 'edit-logo-preview', 'edit-logo-img')">
                            <div id="edit-logo-preview" class="mt-2 hidden">
                                <img src="" id="edit-logo-img" class="h-12 w-12 object-contain rounded-lg border border-slate-200" alt="Logo preview">
                                <p class="text-[10px] text-emerald-600 mt-1 font-bold">Logo je nahrané</p>
                            </div>
                        </div>
                        <div>
                            <label class="label">Banner prevádzky</label>
                            <input type="file" name="banner" class="input-control !p-2 text-xs" onchange="previewImage(this, 'edit-banner-preview', 'edit-banner-img')">
                            <div id="edit-banner-preview" class="mt-2 hidden">
                                <img src="" id="edit-banner-img" class="h-12 w-24 object-cover rounded-lg border border-slate-200" alt="Banner preview">
                                <p class="text-[10px] text-emerald-600 mt-1 font-bold">Banner je nahraný</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-1 gap-3">
                        <div>
                            <label class="label">Popis prevádzky</label>
                            <textarea name="description" id="edit-description" class="input-control" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 transition">Zrušiť</button>
                        <button type="submit" class="px-6 py-2 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition shadow-lg shadow-slate-200">
                            Uložiť zmeny
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('add-modal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeAddModal() {
        document.getElementById('add-modal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function previewImage(input, previewId, imgId = null) {
        const preview = document.getElementById(previewId);
        const img = imgId ? document.getElementById(imgId) : preview.querySelector('img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function openEditModal(profile) {
        const modal = document.getElementById('edit-modal');
        const form = document.getElementById('edit-form');

        // Nastavenie akcie formulára
        form.action = `/admin/profiles/${profile.id}`;

        // Naplnenie polí
        document.getElementById('edit-name').value = profile.name || '';
        document.getElementById('edit-slug').value = profile.slug || '';
        document.getElementById('edit-category').value = profile.category || '';
        document.getElementById('edit-status').value = profile.status || 'draft';
        document.getElementById('edit-city').value = profile.city || '';
        document.getElementById('edit-address').value = profile.address_line1 || '';
        document.getElementById('edit-email').value = profile.email || '';
        document.getElementById('edit-phone').value = profile.phone || '';
        document.getElementById('edit-timezone').value = profile.timezone || 'Europe/Bratislava';
        document.getElementById('edit-owner_id').value = profile.owner_id || '';
        document.getElementById('edit-description').value = profile.description || '';
        document.getElementById('edit-subscription_plan').value = profile.subscription_plan || 'free';

        if (profile.subscription_starts_at) {
            document.getElementById('edit-subscription_starts_at').value = profile.subscription_starts_at.substring(0, 10);
        } else {
            document.getElementById('edit-subscription_starts_at').value = '';
        }

        // Status nahraných obrázkov
        if (profile.logo_url) {
            document.getElementById('edit-logo-preview').classList.remove('hidden');
            document.getElementById('edit-logo-img').src = profile.logo_url;
        } else {
            document.getElementById('edit-logo-preview').classList.add('hidden');
        }

        if (profile.banner_url) {
            document.getElementById('edit-banner-preview').classList.remove('hidden');
            document.getElementById('edit-banner-img').src = profile.banner_url;
        } else {
            document.getElementById('edit-banner-preview').classList.add('hidden');
        }


        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeEditModal() {
        const modal = document.getElementById('edit-modal');
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Vyhľadávanie
    document.getElementById('profile-search')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        const items = document.querySelectorAll('.profile-item');

        items.forEach(item => {
            const name = item.dataset.name;
            const city = item.dataset.city;
            const category = item.dataset.category;

            if (name.includes(query) || city.includes(query) || category.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endsection
