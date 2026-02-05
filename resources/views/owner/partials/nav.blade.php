<div class="flex flex-wrap gap-3 items-center mb-6">
    <a href="{{ route('owner.dashboard') }}" class="admin-tab {{ request()->routeIs('owner.dashboard') ? 'is-active' : '' }}">Dashboard</a>
    <a href="{{ route('owner.schedules') }}" class="admin-tab {{ request()->routeIs('owner.schedules') ? 'is-active' : '' }}">Časy</a>
    <a href="{{ route('owner.services') }}" class="admin-tab {{ request()->routeIs('owner.services') ? 'is-active' : '' }}">Služby</a>
    <a href="{{ route('owner.employees') }}" class="admin-tab {{ request()->routeIs('owner.employees') ? 'is-active' : '' }}">Zamestnanci</a>
    <a href="{{ route('owner.appointments') }}" class="admin-tab {{ request()->routeIs('owner.appointments') ? 'is-active' : '' }}">Rezervácie</a>
    <a href="{{ route('owner.calendar.settings') }}" class="admin-tab {{ request()->routeIs('owner.calendar.settings') ? 'is-active' : '' }}">Kalendár</a>
    <a href="{{ route('owner.holidays') }}" class="admin-tab {{ request()->routeIs('owner.holidays') ? 'is-active' : '' }}">Sviatky</a>
    <a href="{{ route('owner.payments') }}" class="admin-tab {{ request()->routeIs('owner.payments') ? 'is-active' : '' }}">Platby</a>
    <a href="{{ route('owner.billing.settings') }}" class="admin-tab {{ request()->routeIs('owner.billing.settings') ? 'is-active' : '' }}">Fakturačné údaje</a>
</div>
