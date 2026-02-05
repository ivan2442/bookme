<div class="flex flex-wrap gap-3 items-center mb-6">
    <a href="{{ route('admin.dashboard') }}" class="admin-tab {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">Dashboard</a>
    <a href="{{ route('admin.profiles') }}" class="admin-tab {{ request()->routeIs('admin.profiles') ? 'is-active' : '' }}">Prevádzky</a>
    <a href="{{ route('admin.appointments') }}" class="admin-tab {{ request()->routeIs('admin.appointments') ? 'is-active' : '' }}">Rezervácie</a>
    <a href="{{ route('admin.payments') }}" class="admin-tab {{ request()->routeIs('admin.payments') ? 'is-active' : '' }}">Platby</a>
    <a href="{{ route('admin.invoices') }}" class="admin-tab {{ request()->routeIs('admin.invoices') ? 'is-active' : '' }}">Faktúry</a>
    <a href="{{ route('admin.billing.settings') }}" class="admin-tab {{ request()->routeIs('admin.billing.settings') ? 'is-active' : '' }}">Fakturačné údaje</a>
</div>
