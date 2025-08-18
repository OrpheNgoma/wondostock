<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if locale is set in session
        if (Session::has('locale')) {
            $locale = Session::get('locale');
        } else {
            // Default to app locale from config
            $locale = config('app.locale');
        }

        // Ensure the locale is supported
        $availableLocales = array_keys(config('app.available_locales'));
        if (! in_array($locale, $availableLocales)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
