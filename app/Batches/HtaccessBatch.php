<?php

namespace App\Batches;

class HtaccessBatch extends Batch
{
    protected array $commands = [
        'cd {{ env }}',
        'cp ./{{ item }} ./{{ item }}.bak',
        'rm ./{{ item }}',
    ];
}
