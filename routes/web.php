<?php

use App\Facades\Reader;
use App\Factories\BatchFactory;
use App\Http\Controllers\HomeController;
use App\Models\ActiveOnSite;
use App\Services\BatchService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/dev', function () {
    $subsitesTxt = Storage::path('subsites.txt');

    $rawText = Reader::getContentsAsCollection($subsitesTxt);
    $read = false;
    $plugin = '';
    collect($rawText)->each(function ($line) use (&$read, &$plugin) {
        if (str_starts_with($line, 'Sites where')) {
            $plugin = str_replace(['Sites where ', ' is active:'], '', $line);
            $read = true;
        }
        if ($read && str_contains($line, 'https')) {
            $url = preg_replace('/([\d ]+)(.*)/', '$2', $line);
            echo $url . '<br>';
            ActiveOnSite::create([
                'plugin' => $plugin,
                'url' => $url,
            ]);
        }
        if (str_starts_with($line, '[')) {
            $plugin = '';
            $read = false;
        }
    });
});

Route::get('/', function () {
    return view('welcome');
});

Route::post('/parse', function () {
    (new BatchService())->parseCommands();
});

Route::get('/plugin', function () {
    $batchName = 'plugins';
    // process is the key string to access a Batch class
    $process = 'subsite';

    // File must be located in /storage/app/batches
    $batchFile = Storage::path('batches/' . $batchName . '.txt');

    // Get an instance of the appropriate Batch class
    $batch = BatchFactory::build($process);

    return view('batch', ['batch' => $batch, 'batchFile' => $batchFile]);
});


Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/connection', [HomeController::class, 'editConnection'])->name('connection');
