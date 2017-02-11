<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JoshHarington\LaravelTsugi\Controllers\TsugiController;

class ltiSample extends TsugiController {

    public function hello() {
        return view('hello');
    }

    public function goodbye() {
        return view('goodbye');
    }

}
