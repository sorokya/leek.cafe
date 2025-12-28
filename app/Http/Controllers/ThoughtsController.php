<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ThoughtsController extends Controller
{
    public function index(): View
    {
        return view('thoughts.index');
    }
}
