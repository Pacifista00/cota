<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sensor;
use App\Models\Feed;

class MainController extends Controller
{
    public function index(){
        $sensor = Sensor::latest()->first();
        $sensorHistory = Sensor::all();
        $feed = Feed::latest()->first();
        $feedHistory = Feed::all();

        return view('index',[
            'sensor' => $sensor,
            'sensorHistory' => $sensorHistory,
            'feed' => $feed,
            'feedHistory' => $feedHistory,
            'active' => 'monitoring'
        ]);
    }
    public function history(){
        return view('history',[
           'active' => 'history'
        ]);
    }
}
