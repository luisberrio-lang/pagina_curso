<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class LegalController extends Controller
{
    public function terms(): View
    {
        return view('site.legal.terms');
    }

    public function privacy(): View
    {
        return view('site.legal.privacy');
    }

    public function refunds(): View
    {
        return view('site.legal.refunds');
    }

    public function delivery(): View
    {
        return view('site.legal.delivery');
    }

    public function contact(): View
    {
        return view('site.contact');
    }
}
