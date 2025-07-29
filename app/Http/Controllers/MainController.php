<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sensor;
use App\Models\Feed;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $sensor = Sensor::latest()->first();
        $feed = Feed::latest()->first();

        $sevenDays = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $label = date('j-M', strtotime($date));

            $avg = Sensor::select(
                DB::raw('AVG(keasaman) as avg_ph'),
                DB::raw('AVG(suhu) as avg_temp'),
                DB::raw('AVG(kekeruhan) as avg_turb'),
            )
                ->whereDate('created_at', $date)
                ->first();

            $sevenDays->push([
                'date' => $label,
                'avg_ph' => round($avg->avg_ph, 2) ?? null,
                'avg_temp' => round($avg->avg_temp, 2) ?? null,
                'avg_turb' => round($avg->avg_turb, 2) ?? null,
            ]);
        }

        return view('index', [
            'sensor' => $sensor,
            'sensorHistory' => Sensor::all(),
            'feed' => $feed,
            'feedHistory' => Feed::all(),
            'chartLabels' => $sevenDays->pluck('date'),
            'chartPH' => $sevenDays->pluck('avg_ph'),
            'chartTemp' => $sevenDays->pluck('avg_temp'),
            'chartTurb' => $sevenDays->pluck('avg_turb'),
            'active' => 'dashboard'
        ]);
    }
    public function jadwal()
    {
        return view('jadwal', [
            'active' => 'jadwal'
        ]);
    }
    public function riwayat()
    {
        return view('riwayat', [
            'active' => 'riwayat'
        ]);
    }
}
