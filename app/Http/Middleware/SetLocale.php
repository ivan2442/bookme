<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('lang')) {
            $lang = $request->get('lang');
            if (in_array($lang, ['sk', 'en', 'ru', 'ua'])) {
                session(['locale' => $lang]);
            }
        }

        $locale = session('locale', config('app.locale'));

        // Ak je nastavené 'ua', Laravelu povieme že sme v 'ru' pre statické preklady v lang/*.php
        // ale pre Trait HasTranslations si necháme UA ak chceme rozlišovať (ale v Traite som dal premapovanie tiez)
        $laravelLocale = ($locale === 'ua') ? 'ru' : $locale;

        \Illuminate\Support\Facades\App::setLocale($laravelLocale);

        return $next($request);
    }
}
