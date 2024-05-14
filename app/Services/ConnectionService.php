<?php

namespace App\Services;

use App\Models\Connection;

class ConnectionService
{
    public function getConnectionList(): array
    {
        return Connection::all()
            ->pluck('name', 'id')
            ->toArray();
    }
}
