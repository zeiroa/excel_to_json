<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    public function index()
    {
        Excel::load(public_path('a.xlsx'), function($reader) {
            print_r(json_encode($reader->toArray()));
        });
    }
}
