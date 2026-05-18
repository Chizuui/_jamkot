<?php

namespace App\Http\Controllers;

use App\Models\SensorLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class SensorController extends Controller
{
    /**
     * Tampilkan halaman detail sensor DHT22 (Suhu & Kelembapan).
     */
    public function dht22(): View
    {
        $latest = SensorLog::orderBy('created_at', 'desc')->first();
        
        // Data untuk statistik
        $avgTemp = round(SensorLog::avg('suhu') ?? 0, 1);
        $maxTemp = SensorLog::max('suhu') ?? 0;
        $avgHumidity = round(SensorLog::avg('kelembapan') ?? 0, 1);
        $maxHumidity = SensorLog::max('kelembapan') ?? 0;

        // Data untuk grafik (30 data terakhir)
        $logs = SensorLog::orderBy('created_at', 'desc')->take(30)->get()->reverse();
        $chartLabels = $logs->pluck('created_at')->map(fn($date) => $date->format('H:i:s'))->toArray();
        $tempData = $logs->pluck('suhu')->toArray();
        $humidityData = $logs->pluck('kelembapan')->toArray();

        // Data untuk tabel
        $recentLogs = SensorLog::orderBy('created_at', 'desc')->take(10)->get();

        return view('sensor.dht22', compact(
            'latest', 'avgTemp', 'maxTemp', 'avgHumidity', 'maxHumidity',
            'chartLabels', 'tempData', 'humidityData', 'recentLogs'
        ));
    }

    /**
     * Tampilkan halaman detail sensor LDR (Cahaya).
     */
    public function ldr(): View
    {
        $latest = SensorLog::orderBy('created_at', 'desc')->first();
        
        // Data untuk statistik
        $avgLight = round(SensorLog::avg('cahaya') ?? 0, 1);
        $maxLight = SensorLog::max('cahaya') ?? 0;
        $minLight = SensorLog::min('cahaya') ?? 0;
        $total = SensorLog::count();

        // Data untuk grafik (30 data terakhir)
        $logs = SensorLog::orderBy('created_at', 'desc')->take(30)->get()->reverse();
        $chartLabels = $logs->pluck('created_at')->map(fn($date) => $date->format('H:i:s'))->toArray();
        $lightData = $logs->pluck('cahaya')->toArray();

        // Data untuk tabel
        $recentLogs = SensorLog::orderBy('created_at', 'desc')->take(10)->get();

        return view('sensor.ldr', compact(
            'latest', 'avgLight', 'maxLight', 'minLight', 'total',
            'chartLabels', 'lightData', 'recentLogs'
        ));
    }

    /**
     * API untuk data real-time DHT22.
     */
    public function apiDht22(): JsonResponse
    {
        $latest = SensorLog::orderBy('created_at', 'desc')->first();
        
        $avgTemp = round(SensorLog::avg('suhu') ?? 0, 1);
        $maxTemp = SensorLog::max('suhu') ?? 0;
        $avgHumidity = round(SensorLog::avg('kelembapan') ?? 0, 1);
        $maxHumidity = SensorLog::max('kelembapan') ?? 0;

        $logs = SensorLog::orderBy('created_at', 'desc')->take(30)->get()->reverse();
        $labels = $logs->pluck('created_at')->map(fn($date) => $date->format('H:i:s'))->toArray();
        $temp = $logs->pluck('suhu')->toArray();
        $humidity = $logs->pluck('kelembapan')->toArray();

        $rows = SensorLog::orderBy('created_at', 'desc')->take(10)->get()->map(function($log) {
            return [
                'time' => $log->created_at->format('H:i:s'),
                'device' => $log->sensor_id,
                'temp' => $log->suhu,
                'humidity' => $log->kelembapan,
                'pump' => $log->pompa_status,
                'status' => 'Normal' // Default status
            ];
        });

        return response()->json([
            'latest' => [
                'temperature' => $latest->suhu ?? 0,
                'humidity' => $latest->kelembapan ?? 0,
                'timestamp' => $latest ? $latest->created_at->format('H:i:s') : '--'
            ],
            'stats' => [
                'avgTemp' => $avgTemp,
                'maxTemp' => $maxTemp,
                'avgHumidity' => $avgHumidity,
                'maxHumidity' => $maxHumidity
            ],
            'labels' => $labels,
            'temp' => $temp,
            'humidity' => $humidity,
            'rows' => $rows
        ]);
    }

    /**
     * API untuk data real-time LDR.
     */
    public function apiLdr(): JsonResponse
    {
        $latest = SensorLog::orderBy('created_at', 'desc')->first();
        
        $avgLight = round(SensorLog::avg('cahaya') ?? 0, 1);
        $maxLight = SensorLog::max('cahaya') ?? 0;
        $minLight = SensorLog::min('cahaya') ?? 0;
        $total = SensorLog::count();

        $logs = SensorLog::orderBy('created_at', 'desc')->take(30)->get()->reverse();
        $labels = $logs->pluck('created_at')->map(fn($date) => $date->format('H:i:s'))->toArray();
        $light = $logs->pluck('cahaya')->toArray();

        $rows = SensorLog::orderBy('created_at', 'desc')->take(10)->get()->map(function($log) {
            return [
                'time' => $log->created_at->format('H:i:s'),
                'device' => $log->sensor_id,
                'light' => $log->cahaya,
                'pump' => $log->pompa_status,
                'status' => 'Normal' // Default status
            ];
        });

        return response()->json([
            'latest' => [
                'light_intensity' => $latest->cahaya ?? 0,
                'timestamp' => $latest ? $latest->created_at->format('H:i:s') : '--'
            ],
            'stats' => [
                'avg' => $avgLight,
                'max' => $maxLight,
                'min' => $minLight,
                'total' => $total
            ],
            'labels' => $labels,
            'light' => $light,
            'rows' => $rows
        ]);
    }
}
