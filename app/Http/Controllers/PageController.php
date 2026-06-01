<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return \view('pages.about');
    }

    public function careers()
    {
        return \view('pages.careers');
    }

    public function privacy()
    {
        return \view('pages.privacy');
    }

    public function terms()
    {
        return \view('pages.terms');
    }

    public function helpCenter()
    {
        return \view('pages.help-center');
    }

    public function community()
    {
        return \view('pages.community');
    }

    public function status()
    {
        return \view('pages.status');
    }
}
