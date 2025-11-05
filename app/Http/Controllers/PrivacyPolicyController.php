<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PrivacyPolicyController extends Controller
{
    /**
     * Display privacy policy page
     * 
     * This page is publicly accessible (no authentication required)
     * Required for Google Play Store publication compliance
     * 
     * @return View
     */
    public function show(): View
    {
        return view('privacy-policy');
    }
}

