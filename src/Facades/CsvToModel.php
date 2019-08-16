<?php

namespace Kakposoe\CsvToModel\Facades;

use Illuminate\Support\Facades\Facade;

class CsvToModel extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'csvtomodel';
    }
}
