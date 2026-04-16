<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingBanner;
use Illuminate\View\View;

class LandingBannersIndexController extends Controller
{
    public function __invoke(): View
    {
        $banners = LandingBanner::query()->orderBy('order')->get();

        return view('admin.landing-banners.index', [
            'banners' => $banners,
        ]);
    }
}
