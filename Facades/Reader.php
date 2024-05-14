<?php

namespace App\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ?string contents(string $filenname);
 * @method static ?array getContentsAsArray(string $filenname);
 * @method static ?Collection getContentsAsCollection(string $filename);
 */
class Reader extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'reader';
    }

}
