<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class BatchService
{
    public function parseCommands()
    {
        $service = new ShellService();
        $commands = request('commands');

//        $lines = explode("\n", $commands);
        $service->connect('Pagely');
        $result = $service->execute($commands);

        Log::debug($result);

        return response()->json(['success' => true]);
    }
}
