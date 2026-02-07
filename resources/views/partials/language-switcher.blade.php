@php
    $currentLocale = session('locale', config('app.locale'));
    $locales = [
        'sk' => ['name' => 'Slovensky', 'svg' => '<svg viewBox="0 0 640 480"><g fill-rule="evenodd" stroke-width="1pt"><path fill="#fff" d="M0 0h640v480H0z"/><path fill="#0b4ea2" d="M0 160h640v320H0z"/><path fill="#ee1c25" d="M0 320h640v160H0z"/><g transform="matrix(1.238 0 0 1.238 181.8 259.6)"><path fill="#fff" d="M0-112.5c-37.5 0-60 22.5-60 60 0 42.5 35 70 60 82.5 25-12.5 60-40 60-82.5 0-37.5-22.5-60-60-60z"/><path fill="#ee1c25" d="M0-100c-30 0-50 17.5-50 50 0 35 30 60 50 70 20-10 50-35 50-70 0-32.5-20-50-50-50z"/><path fill="#0b4ea2" d="M-30-10h60v10h-60zm10 10h40v10h-40zM-1.2-40h2.4v30h-2.4zm-15 0h30v2.4h-30z"/></g></g></svg>'],
        'en' => ['name' => 'English', 'svg' => '<svg viewBox="0 0 640 480"><path fill="#012169" d="M0 0h640v480H0z"/><path fill="#FFF" d="m75 0 244 181L562 0h78v62L400 240l240 178v62h-78L320 300 78 480H0v-62l240-178L0 62V0h75z"/><path fill="#C8102E" d="m424 281 216 159v40L369 281h55zM640 0v3L391 191h55L640 0zM0 480v-3l249-191h-55L0 480zM0 0v40l216 151h55L0 0z"/><path fill="#FFF" d="M240 0v480h160V0H240zM0 160v160h640V160H0z"/><path fill="#C8102E" d="M280 0v480h80V0h-80zM0 200v80h640v-80H0z"/></svg>'],
        'ua' => ['name' => 'Українська', 'svg' => '<svg viewBox="0 0 640 480"><g fill-rule="evenodd" stroke-width="1pt"><path fill="#ffd500" d="M0 0h640v480H0z"/><path fill="#005bbb" d="M0 0h640v240H0z"/></g></svg>'],
    ];
@endphp

<div class="relative inline-block text-left language-dropdown">
    <div>
        <button type="button" class="flag-circle active focus:outline-none language-dropdown-button" aria-expanded="false" aria-haspopup="true">
            {!! $locales[$currentLocale]['svg'] !!}
        </button>
    </div>

    <div class="hidden absolute right-0 mt-2 w-12 rounded-xl bg-white shadow-2xl border border-slate-100 ring-1 ring-black ring-opacity-5 z-50 language-dropdown-menu">
        <div class="py-1 flex flex-col items-center gap-1" role="menu" aria-orientation="vertical">
            @foreach($locales as $code => $data)
                @if($code !== $currentLocale)
                    <a href="?lang={{ $code }}" class="flag-circle hover:bg-slate-50 transition p-0.5" role="menuitem" title="{{ $data['name'] }}">
                        {!! $data['svg'] !!}
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</div>

<style>
    .flag-circle {
        width: 28px;
        height: 28px;
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
        transform: scale(1.1);
        border-color: #10b981;
    }
    .flag-circle.active {
        border-color: #10b981;
        box-shadow: 0 4px 10px -2px rgba(16, 185, 129, 0.2);
    }
    .flag-circle svg {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scale(1.5);
    }
    .language-dropdown-menu.hidden {
        display: none;
    }
    .language-dropdown-menu:not(.hidden) {
        display: flex;
        flex-direction: column;
    }
</style>

<script>
if (!window.languageSwitcherInitialized) {
    window.languageSwitcherInitialized = true;
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.language-dropdown-button');

        if (button) {
            e.preventDefault();
            e.stopPropagation();
            const container = button.closest('.language-dropdown');
            const menu = container.querySelector('.language-dropdown-menu');
            const isHidden = menu.classList.contains('hidden');

            // Close all other menus
            document.querySelectorAll('.language-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.language-dropdown-button').forEach(b => b.setAttribute('aria-expanded', 'false'));

            if (isHidden) {
                menu.classList.remove('hidden');
                button.setAttribute('aria-expanded', 'true');
            }
        } else {
            // Clicked outside
            if (!e.target.closest('.language-dropdown-menu')) {
                document.querySelectorAll('.language-dropdown-menu').forEach(m => m.classList.add('hidden'));
                document.querySelectorAll('.language-dropdown-button').forEach(b => b.setAttribute('aria-expanded', 'false'));
            }
        }
    });
}
</script>
