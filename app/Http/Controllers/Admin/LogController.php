<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogController extends Controller
{
    public function index()
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logPath)) {
            $content = File::get($logPath);
            
            // Split by log entry start [YYYY-MM-DD HH:MM:SS]
            // We capture the timestamp as well
            $parts = preg_split('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/m', $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            
            // Expected parts: [timestamp, message, timestamp, message, ...]
            $id = 1;
            // Iterate backwards to get latest logs first
            for ($i = count($parts) - 2; $i >= 0; $i -= 2) {
                $rawTime = $parts[$i];
                $rawMessage = $parts[$i+1] ?? '';
                
                // Parse: " local.INFO: Message content"
                if (preg_match('/^\s*(\w+)\.(\w+):\s*(.*)/s', $rawMessage, $m)) {
                    $level = strtoupper($m[2]);
                    $message = trim($m[3]);

                    $type = 'info';
                    if (in_array($level, ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'])) {
                        $type = 'error';
                    } elseif ($level === 'SUCCESS') {
                        $type = 'success';
                    }

                    // Extract Payload/Response if present
                    $payload = '-';
                    $response = '-';
                    if (preg_match('/Payload:\s*(\{.*?\})/s', $message, $pMatch)) {
                        $payload = $pMatch[1];
                    }
                    if (preg_match('/Response:\s*(\{.*?\})/s', $message, $rMatch)) {
                        $response = $rMatch[1];
                    }

                    // Clean message for summary
                    $displayMessage = trim(preg_replace('/(Payload|Response):\s*\{.*?\}/s', '', $message));
                    if (strlen($displayMessage) > 120) {
                        $displayMessage = mb_substr($displayMessage, 0, 117) . '...';
                    }

                    $logs[] = [
                        'id' => $id++,
                        'type' => $type,
                        'time' => date('H:i:s', strtotime($rawTime)),
                        'date' => date('d.m.Y', strtotime($rawTime)),
                        'endpoint' => $displayMessage ?: $level,
                        'method' => '-',
                        'status' => '-',
                        'payload' => $payload !== '-' ? $payload : $message,
                        'response' => $response !== '-' ? $response : 'Bkz: laravel.log',
                    ];
                }

                if ($id > 100) break; // Limit to 100 entries
            }
        }

        return view('admin.logs', compact('logs'));
    }

    public function clear()
    {
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            File::put($logPath, '');
        }
        return back()->with('success', 'Log dosyası başarıyla temizlendi.');
    }
}
