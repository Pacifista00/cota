<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sensor;
use App\Models\Feed;
use App\Models\FeedSchedule;
use App\Models\FeedExecution;
use App\Models\Pond;
use App\Services\FeedSchedulingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    public function index()
    {
        // $sensor = Sensor::latest()->first();
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
            // 'sensor' => $sensor,
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

    public function riwayatSensor()
    {
        return view('riwayat-sensor', [
            'active' => 'riwayat_sensor',
            'sensorHistories' => Sensor::orderBy('created_at', 'desc')->paginate(20),
        ]);
    }
    public function riwayatPakan(Request $request)
    {
        // Get filter parameters
        $status = $request->get('status');
        $triggerType = $request->get('trigger_type');
        $perPage = $request->get('per_page', 20);

        // Build query with eager loading to prevent N+1
        $query = FeedExecution::with('schedule:id,name')
            ->orderBy('updated_at', 'desc');

        // Apply filters
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($triggerType && $triggerType !== 'all') {
            $query->where('trigger_type', $triggerType);
        }

        // Get paginated results
        $feedHistories = $query->paginate($perPage)->withQueryString();

        // Calculate statistics
        $statistics = [
            'total' => FeedExecution::count(),
            'success' => FeedExecution::successful()->count(),
            'failed' => FeedExecution::failed()->count(),
            'pending' => FeedExecution::pending()->count(),
            'manual' => FeedExecution::manual()->count(),
            'scheduled' => FeedExecution::scheduled()->count(),
        ];

        return view('riwayat-pakan', [
            'active' => 'riwayat_pakan',
            'feedHistories' => $feedHistories,
            'statistics' => $statistics,
            'filters' => [
                'status' => $status,
                'trigger_type' => $triggerType,
                'per_page' => $perPage,
            ],
        ]);
    }
    public function tambak()
    {
        $idUser = auth()->id();
        return view('tambak', [
            'active' => 'tambak',
            'ponds' =>  Pond::where('user_id', $idUser)->get()
        ]);
    }
    public function preview()
    {
        return view('preview', [
            'active' => 'preview',
        ]);
    }

    /**
     * Jadwal Terjadwal - Index
     */
    public function jadwalTerjadwal()
    {
        $userId = auth()->id();
        
        // Get all schedules for authenticated user
        $schedules = FeedSchedule::when($userId, function ($q) use ($userId) {
            return $q->where('user_id', $userId);
        })
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $statistics = [
            'total' => $schedules->count(),
            'active' => $schedules->where('is_active', true)->count(),
            'inactive' => $schedules->where('is_active', false)->count(),
            'executed_today' => $schedules->filter(function ($schedule) {
                return $schedule->was_executed_today;
            })->count(),
        ];

        return view('jadwal-terjadwal', [
            'active' => 'jadwal_terjadwal',
            'schedules' => $schedules,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Jadwal Terjadwal - Store
     */
    public function storeJadwalTerjadwal(Request $request, FeedSchedulingService $service)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'waktu_pakan' => 'required|date_format:H:i',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'waktu_pakan.required' => 'Waktu pakan wajib diisi.',
            'waktu_pakan.date_format' => 'Format waktu harus HH:MM.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini.',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ]);

        try {
            // Convert HH:MM to HH:MM:SS
            $validated['waktu_pakan'] = $validated['waktu_pakan'] . ':00';
            $validated['user_id'] = auth()->id();
            $validated['is_active'] = true;
            $validated['frequency_type'] = 'daily';

            $service->createSchedule($validated);

            return redirect()->back()->with('success', 'Jadwal pakan berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan jadwal: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Jadwal Terjadwal - Update
     */
    public function updateJadwalTerjadwal(Request $request, $id, FeedSchedulingService $service)
    {
        $schedule = FeedSchedule::findOrFail($id);

        // Authorization check
        if ($schedule->user_id && $schedule->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah jadwal ini.');
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'waktu_pakan' => 'sometimes|required|date_format:H:i',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'waktu_pakan.date_format' => 'Format waktu harus HH:MM.',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ]);

        try {
            // Convert HH:MM to HH:MM:SS if waktu_pakan exists
            if (isset($validated['waktu_pakan'])) {
                $validated['waktu_pakan'] = $validated['waktu_pakan'] . ':00';
            }

            $service->updateSchedule($schedule, $validated);

            return redirect()->back()->with('success', 'Jadwal pakan berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui jadwal: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Jadwal Terjadwal - Delete
     */
    public function deleteJadwalTerjadwal($id, FeedSchedulingService $service)
    {
        $schedule = FeedSchedule::findOrFail($id);

        // Authorization check
        if ($schedule->user_id && $schedule->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus jadwal ini.');
        }

        try {
            $service->deleteSchedule($schedule);

            return redirect()->back()->with('success', 'Jadwal pakan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Jadwal Terjadwal - Toggle Active
     */
    public function toggleJadwalTerjadwal($id, FeedSchedulingService $service)
    {
        $schedule = FeedSchedule::findOrFail($id);

        // Authorization check
        if ($schedule->user_id && $schedule->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah jadwal ini.');
        }

        try {
            if ($schedule->is_active) {
                $service->deactivateSchedule($schedule);
                $message = 'Jadwal pakan berhasil dinonaktifkan!';
            } else {
                $service->activateSchedule($schedule);
                $message = 'Jadwal pakan berhasil diaktifkan!';
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah status jadwal: ' . $e->getMessage());
        }
    }
}
