<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switchLanguage($locale)
    {
        // Check if the locale is supported
        if (!in_array($locale, ['en', 'ru'])) {
            $locale = 'en';
        }
        
        // Set the application locale
        App::setLocale($locale);
        Session::put('locale', $locale);
        
        return redirect()->back();
    }
}