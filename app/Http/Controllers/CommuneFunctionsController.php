<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class CommuneFunctionsController extends Controller
{
    public function DateFormYMD($date)
    {
        $date = Carbon::parse($date);
        $date = $date->format('Y-m-d');
        return $date;
    }
    public function TimeFormHM($time)
    {
        $time = Carbon::parse($time);
        $time = $time->format('H:i');
        return $time;
    }
}
