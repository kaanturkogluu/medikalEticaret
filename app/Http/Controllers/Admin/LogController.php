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
            
            // Laravel log pattern
            // Pattern 1: [2026-04-16 22:04:53] local.INFO: message
            // Pattern 2: [2026-04-16 22:04:53] local.ERROR: message {"exception":"..."}
            
            $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*?)(?=\s*\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]|\s*$)/s';
            
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

            $id = 1;
            foreach (array_reverse($matches) as $match) {
                $rawTime = $match[1];
                $level = strtoupper($match[3]);
                $message = $match[4];

                $type = 'info';
                if (in_array($level, ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'])) {
                    $type = 'error';
                } elseif (in_array($level, ['SUCCESS'])) {
                    $type = 'success';
                }

                // Try to find JSON in message for request/response style logging
                $payload = '-';
                $response = '-';
                
                if (preg_match('/Payload: (\{.*?\})/s', $message, $pMatch)) {
                    $payload = $pMatch[1];
                }
                if (preg_match('/Response: (\{.*?\})/s', $message, $rMatch)) {
                    $response = $rMatch[1];
                }
                
                // Clean message for display
                $displayMessage = trim(preg_replace('/(Payload|Response): \{.*?\}/s', '', $message));
                if (strlen($displayMessage) > 100) {
                    $displayMessage = substr($displayMessage, 0, 97) . '...';
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
                    'response' => $response !== '-' ? $response : 'Detailed info in laravel.log',
                ];

                if ($id > 50) break;
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
