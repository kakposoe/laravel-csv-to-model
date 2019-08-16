<?php

namespace Kakposoe\CsvToModel\Tests\Support\TestModels;

use Illuminate\Database\Eloquent\Model;
use Kakposoe\CsvToModel\Traits\CsvToModel;

class TestModel extends Model
{
    use CsvToModel;

    protected $table   = 'test_models';
    protected $guarded = [];
    public $timestamps = false;
}
