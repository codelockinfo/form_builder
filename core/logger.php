<?php
if (!function_exists('generate_log')) {
    function generate_log($type, $message, $data = [])
    {
        $logDir = ABS_PATH . '/logs';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $file = $logDir . '/app-log-' . date('Y-m-d') . '.log';

        $log = [
            'datetime' => date('Y-m-d H:i:s'),
            'type'     => $type,
            'message'  => $message,
            'data'     => $data,
            'ip'       => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            'method'   => $_SERVER['REQUEST_METHOD'] ?? '',
            'uri'      => $_SERVER['REQUEST_URI'] ?? ''
        ];

        file_put_contents(
            
            $file,
            "----------------------------------\n" . json_encode($log, JSON_PRETTY_PRINT) . "\n",
            FILE_APPEND
        );
    }
}
