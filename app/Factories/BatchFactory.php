<?php

namespace App\Factories;

use App\Batches\HtaccessBatch;
use App\Batches\HtaccessRestoreBatch;
use App\Batches\PluginBatch;
use App\Batches\SubsitePluginBatch;
use App\Interfaces\BatchInterface;

class BatchFactory
{
    public static function build(string $process): BatchInterface
    {
        return match ($process) {
            'hta_remove' => new HtaccessBatch(),
            'hta_restore' => new HtaccessRestoreBatch(),
            'plugins' => new PluginBatch(),
            'subsite' => new SubsitePluginBatch(),
        };
    }
}
