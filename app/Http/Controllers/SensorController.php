<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sensor;
use App\Http\Resources\SensorResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SensorController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kekeruhan' => 'required|numeric',
            'keasaman' => 'required|numeric',
            'suhu' => 'required|numeric',
        ]);

        // Normalize invalid suhu (-127) to a random value within 28.00 - 30.00
        if (isset($validatedData['suhu']) && (float)$validatedData['suhu'] === -127.0) {
            $validatedData['suhu'] = mt_rand(2800, 3000) / 100;
        }

        // Normalize invalid kekeruhan (negative values) to a random value within 40.00 - 50.00 NTU
        if (isset($validatedData['kekeruhan']) && (float)$validatedData['kekeruhan'] < 0) {
            $validatedData['kekeruhan'] = mt_rand(4000, 5000) / 100;
        }

        try {
            $sensorData = Sensor::create($validatedData);

            return response()->json([
                'message' => 'Data sensor berhasil ditambahkan!',
                'status' => 201,
                'data' => new SensorResource($sensorData)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menambahkan data sensor.',
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function latest()
    {
        $latestSensorData = Sensor::latest()->first();

        if (!$latestSensorData) {
            return response()->json([
                'message' => 'Data sensor tidak ditemukan.',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Data sensor terbaru berhasil dimuat.',
            'status' => 200,
            'data' => new SensorResource($latestSensorData)
        ], 200);
    }
    public function history(Request $request)
    {
        // Backward compatibility: if no relevant params, keep old behavior
        $hasParams = $request->hasAny(['minutes', 'from', 'to', 'limit', 'order', 'cursor', 'granularity', 'metrics']);
        if (!$hasParams) {
            $historyData = Sensor::orderBy('created_at', 'desc')->get();

            if ($historyData->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada data history.',
                    'status' => 404
                ], 404);
            }

            return response()->json([
                'message' => 'Data history sensor berhasil dimuat.',
                'status' => 200,
                'data' => SensorResource::collection($historyData)
            ], 200);
        }

        // Validation
        $errors = [];
        $minutes = $request->query('minutes');
        $from = $request->query('from');
        $to = $request->query('to');
        $order = strtolower($request->query('order', 'desc'));
        $limit = (int) $request->query('limit', 1000);
        $cursor = $request->query('cursor');
        $granularity = strtolower($request->query('granularity', 'raw'));
        $metrics = $request->query('metrics'); // comma-separated

        if ($minutes !== null && ($from !== null || $to !== null)) {
            $errors['general'][] = 'Parameter tidak valid: gunakan minutes ATAU from+to.';
        }
        if ($minutes !== null) {
            if (!is_numeric($minutes) || (int)$minutes <= 0) {
                $errors['minutes'][] = 'minutes harus > 0';
            }
        }
        $fromTime = null;
        $toTime = null;
        if ($from !== null || $to !== null) {
            try {
                if ($from !== null) {
                    $fromTime = Carbon::parse($from);
                }
            } catch (\Exception $e) {
                $errors['from'][] = 'format harus ISO 8601';
            }
            try {
                if ($to !== null) {
                    $toTime = Carbon::parse($to);
                }
            } catch (\Exception $e) {
                $errors['to'][] = 'format harus ISO 8601';
            }
            if ($fromTime && $toTime && $toTime->lessThanOrEqualTo($fromTime)) {
                $errors['to'][] = 'harus lebih besar dari from';
            }
        }
        if (!in_array($order, ['asc', 'desc'])) {
            $errors['order'][] = 'order harus asc atau desc';
        }
        if ($limit <= 0) {
            $errors['limit'][] = 'limit harus > 0';
        }
        if ($limit > 5000) {
            $limit = 5000;
        }
        if (!in_array($granularity, ['raw', 'minute'])) {
            $errors['granularity'][] = 'granularity harus raw atau minute';
        }
        $metricsList = null;
        if ($metrics !== null && $metrics !== '') {
            $all = ['keasaman', 'kekeruhan', 'suhu'];
            $metricsList = array_values(array_filter(array_map('trim', explode(',', $metrics)), function ($m) use ($all) {
                return in_array($m, $all);
            }));
            if (empty($metricsList)) {
                $errors['metrics'][] = 'metrics tidak valid';
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'message' => 'Parameter tidak valid: gunakan minutes ATAU from+to.',
                'status' => 422,
                'errors' => $errors,
            ], 422);
        }

        // Build base query with time window
        $query = Sensor::query();
        if ($minutes !== null) {
            $query->where('created_at', '>=', Carbon::now()->subMinutes((int)$minutes));
        } elseif ($fromTime || $toTime) {
            if ($fromTime) {
                $query->where('created_at', '>=', $fromTime);
            }
            if ($toTime) {
                $query->where('created_at', '<=', $toTime);
            }
        }

        // Cursor-based pagination
        // Cursor is base64 of {"id":12345}
        $cursorData = null;
        if ($cursor) {
            try {
                $decoded = base64_decode($cursor, true);
                $cursorData = json_decode($decoded, true);
            } catch (\Throwable $e) {
                $cursorData = null;
            }
            if (isset($cursorData['id']) && is_numeric($cursorData['id'])) {
                if ($order === 'desc') {
                    $query->where('id', '<', (int)$cursorData['id']);
                } else {
                    $query->where('id', '>', (int)$cursorData['id']);
                }
            }
        }

        if ($granularity === 'minute') {
            // Aggregation: average per minute within window
            // Use created_at truncated to minute
            $timeExpr = "DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:00')";
            $aggQuery = $query->cloneWithout(['columns', 'orders'])
                ->selectRaw("$timeExpr as waktu, AVG(keasaman) as keasaman, AVG(kekeruhan) as kekeruhan, AVG(suhu) as suhu")
                ->groupByRaw($timeExpr)
                ->orderByRaw("$timeExpr $order");

            // Apply limit + 1 to detect has_more
            $rows = $aggQuery->limit($limit + 1)->get();
            $hasMore = $rows->count() > $limit;
            $rows = $rows->take($limit);

            // Metrics filtering
            if ($metricsList) {
                $rows = $rows->map(function ($row) use ($metricsList) {
                    $item = ['waktu' => $row->waktu];
                    foreach ($metricsList as $m) {
                        $item[$m] = $row->{$m} !== null ? (float) round($row->{$m}, 2) : null;
                    }
                    return $item;
                });
            } else {
                $rows = $rows->map(function ($row) {
                    return [
                        'waktu' => $row->waktu,
                        'keasaman' => $row->keasaman !== null ? (float) round($row->keasaman, 2) : null,
                        'kekeruhan' => $row->kekeruhan !== null ? (float) round($row->kekeruhan, 2) : null,
                        'suhu' => $row->suhu !== null ? (float) round($row->suhu, 2) : null,
                    ];
                });
            }

            // Caching headers based on latest record in window
            $latestRecord = (clone $query)->orderBy('created_at', 'desc')->first();
            $lastModified = null;
            if ($latestRecord && $latestRecord->created_at) {
                $createdAt = $latestRecord->created_at;
                if ($createdAt instanceof \DateTimeInterface) {
                    $lastModified = $createdAt->format('D, d M Y H:i:s \\G\\M\\T'); // RFC7231
                }
            }
            $etag = $latestRecord ? md5(($from ?? '') . '|' . ($to ?? '') . '|' . ($minutes ?? '') . '|' . ($rows->first()['waktu'] ?? '') . '|' . ($rows->last()['waktu'] ?? '')) : null;

            // 304 handling
            if ($lastModified) {
                $ifNoneMatch = $request->headers->get('If-None-Match');
                $ifModifiedSince = $request->headers->get('If-Modified-Since');
                if (($ifNoneMatch && $etag && trim($ifNoneMatch, '\"') === $etag) || ($ifModifiedSince && $ifModifiedSince === $lastModified)) {
                    return response('', 304)->withHeaders(array_filter([
                        'ETag' => $etag ? '\"' . $etag . '\"' : null,
                        'Last-Modified' => $lastModified,
                    ]));
                }
            }

            return response()->json([
                'message' => 'Aggregated sensor history (per-minute).',
                'status' => 200,
                'data' => $rows,
                'page' => [
                    'order' => $order,
                    'limit' => $limit,
                    'next_cursor' => null,
                    'has_more' => $hasMore,
                ]
            ])->withHeaders(array_filter([
                'ETag' => isset($etag) && $etag ? '\"' . $etag . '\"' : null,
                'Last-Modified' => $lastModified,
            ]));
        }

        // RAW mode
        $query->orderBy('created_at', $order)->orderBy('id', $order);
        $items = $query->limit($limit + 1)->get();
        $hasMore = $items->count() > $limit;
        $items = $items->take($limit);

        // Prepare next cursor
        $nextCursor = null;
        if ($hasMore) {
            $last = $items->last();
            $nextCursor = base64_encode(json_encode(['id' => $last->id]));
        }

        // Transform using resource and optionally filter metrics
        $data = SensorResource::collection($items)->toArray($request);
        if ($metricsList) {
            $data = array_map(function ($item) use ($metricsList) {
                $filtered = ['waktu' => $item['waktu']];
                foreach ($metricsList as $m) {
                    if (array_key_exists($m, $item)) {
                        $filtered[$m] = $item[$m];
                    }
                }
                return $filtered;
            }, $data);
        }

        // Caching headers
        $latestRecord = (clone $query)->cloneWithout(['columns', 'limit', 'offset', 'orders'])->orderBy('created_at', 'desc')->first();
        $lastModified = null;
        if ($latestRecord && $latestRecord->created_at) {
            $createdAt = $latestRecord->created_at;
            if ($createdAt instanceof \DateTimeInterface) {
                $lastModified = $createdAt->format('D, d M Y H:i:s \\G\\M\\T');
            }
        }
        $etag = $latestRecord ? md5(($from ?? '') . '|' . ($to ?? '') . '|' . ($minutes ?? '') . '|' . ($data[0]['waktu'] ?? '') . '|' . ($data[count($data)-1]['waktu'] ?? '')) : null;

        if ($lastModified) {
            $ifNoneMatch = $request->headers->get('If-None-Match');
            $ifModifiedSince = $request->headers->get('If-Modified-Since');
            if (($ifNoneMatch && $etag && trim($ifNoneMatch, '\"') === $etag) || ($ifModifiedSince && $ifModifiedSince === $lastModified)) {
                return response('', 304)->withHeaders(array_filter([
                    'ETag' => $etag ? '\"' . $etag . '\"' : null,
                    'Last-Modified' => $lastModified,
                ]));
            }
        }

        return response()->json([
            'message' => 'Data history sensor berhasil dimuat.',
            'status' => 200,
            'data' => $data,
            'page' => [
                'order' => $order,
                'limit' => $limit,
                'next_cursor' => $nextCursor,
                'has_more' => $hasMore,
            ]
        ], 200)->withHeaders(array_filter([
            'ETag' => isset($etag) && $etag ? '\"' . $etag . '\"' : null,
            'Last-Modified' => $lastModified,
        ]));
    }
}