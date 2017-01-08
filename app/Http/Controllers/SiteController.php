<?php

namespace App\Http\Controllers;

class SiteController extends Controller
{
    public function guest()
    {
        return view('site.guest');
    }

    public function home()
    {
        return view('site.home');
    }
}
