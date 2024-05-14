<?php

namespace App\Batches;

class HtaccessRestoreBatch extends Batch
{
    protected array $commands = [
        'cd {{ env }}',
        'mv ./{{ item }}.bak ./{{ item }}',
    ];
}
