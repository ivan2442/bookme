@php
    $currentLocale = session('locale', config('app.locale'));
@endphp

<div class="flex items-center gap-2">
    <a href="?lang=sk" class="flag-circle {{ $currentLocale === 'sk' ? 'active' : '' }}" title="Slovensky">
        <svg viewBox="0 0 640 480"><g fill-rule="evenodd" stroke-width="1pt"><path fill="#fff" d="M0 0h640v480H0z"/><path fill="#0b4ea2" d="M0 160h640v320H0z"/><path fill="#ee1c25" d="M0 320h640v160H0z"/><g transform="matrix(1.238 0 0 1.238 181.8 259.6)"><path fill="#fff" d="M0-112.5c-37.5 0-60 22.5-60 60 0 42.5 35 70 60 82.5 25-12.5 60-40 60-82.5 0-37.5-22.5-60-60-60z"/><path fill="#ee1c25" d="M0-100c-30 0-50 17.5-50 50 0 35 30 60 50 70 20-10 50-35 50-70 0-32.5-20-50-50-50z"/><path fill="#0b4ea2" d="M-30-10h60v10h-60zm10 10h40v10h-40zM-1.2-40h2.4v30h-2.4zm-15 0h30v2.4h-30z"/></g></g></svg>
    </a>
    <a href="?lang=en" class="flag-circle {{ $currentLocale === 'en' ? 'active' : '' }}" title="English">
        <svg viewBox="0 0 640 480"><path fill="#012169" d="M0 0h640v480H0z"/><path fill="#FFF" d="m75 0 244 181L562 0h78v62L400 240l240 178v62h-78L320 300 78 480H0v-62l240-178L0 62V0h75z"/><path fill="#C8102E" d="m424 281 216 159v40L369 281h55zM640 0v3L391 191h55L640 0zM0 480v-3l249-191h-55L0 480zM0 0v40l216 151h55L0 0z"/><path fill="#FFF" d="M240 0v480h160V0H240zM0 160v160h640V160H0z"/><path fill="#C8102E" d="M280 0v480h80V0h-80zM0 200v80h640v-80H0z"/></svg>
    </a>
    <a href="?lang=ua" class="flag-circle {{ $currentLocale === 'ua' ? 'active' : '' }}" title="Українська (RU)">
        <svg viewBox="0 0 640 480"><g fill-rule="evenodd" stroke-width="1pt"><path fill="#ffd500" d="M0 0h640v480H0z"/><path fill="#005bbb" d="M0 0h640v240H0z"/></g></svg>
    </a>
</div>

<style>
    .flag-circle {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 2px solid transparent;
        transition: all 0.2s;
        cursor: pointer;
        background: white;
        flex-shrink: 0;
    }
    .flag-circle:hover {
        transform: scale(1.15);
        border-color: #10b981;
    }
    .flag-circle.active {
        border-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
    }
    .flag-circle svg {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scale(1.5);
    }
</style>
