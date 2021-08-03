<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Foundation\Application;

class AppController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index(): Application|Factory|View
    {
        return view('home');
    }
}
