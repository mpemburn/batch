<?php

namespace App\Console\Commands;

use App\Factories\BatchFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BatchProcessCommand extends Command
{
    protected $signature = 'app:batch {--conn=} {--dir=} {--batch=} {--process=}';
    // artisan app:batch --batch=htaccess_test --process=hta_remove
    // artisan app:batch --batch=htaccess_prod --process=hta_remove
    // artisan app:batch --batch=htaccess_test --process=hta_restore
    // artisan app:batch --batch=htaccess_prod --process=hta_restore

    protected $description = 'Batch process shell commands';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // conn must be the name of an existing connection (can be set in batch file with conn=)
        $connection = $this->option('conn');
        // dir is the directory path (can be set in batch file with dir=)
        $dir = $this->option('dir');
        // batch is a text file with appropriate command strings. Leave off .txt extension
        $batchName = $this->option('batch');
        // process is the key string to access a Batch class
        $process = $this->option('process');

        // File must be located in /storage/app/batches
        $batchFile = Storage::path('batches/' . $batchName . '.txt');

        // Get an instance of the appropriate Batch class
        $batch = BatchFactory::build($process);

        $batch->setConnection($connection)
            ->setWorkingDirectory($dir)
            ->getBatchFile($batchFile)
            ->run();
    }
}
