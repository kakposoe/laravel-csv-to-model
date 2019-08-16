<?php

namespace Kakposoe\CsvToModel\Traits;

trait CsvToModel
{

    public static function csv($path)
    {
        return (new \Kakposoe\CsvToModel\CsvToModel($path, self::class));
    }
}
