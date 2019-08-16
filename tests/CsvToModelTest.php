<?php

namespace Kakposoe\CsvToModel\Tests;

use Kakposoe\CsvToModel\Facades\CsvToModel;
use Kakposoe\CsvToModel\Tests\Support\TestModels\TestModel;
use Kakposoe\CsvToModel\CsvToModelServiceProvider as ServiceProvider;

class CsvToModelTest extends TestCase
{
    /** @test */
    public function can_import_data_from_csv_to_model()
    {
        $path = __DIR__ . '/Support/testfiles/test_import.csv';

        TestModel::csv($path)->import();

        $this->assertCount(1, TestModel::all());
    }

    /** @test */
    public function can_change_headers_automatically()
    {
        $path = __DIR__ . '/Support/testfiles/test_string_headers_import.csv';

        TestModel::csv($path)->import();
        
        $this->assertCount(1, TestModel::all());
    }

    /** @test */
    public function can_replace_headers()
    {
        $path = __DIR__ . '/Support/testfiles/test_change_headers_import.csv';

        TestModel::csv($path)
            ->headers(['Email Address' => 'email'])
            ->import();
        
        $this->assertCount(1, TestModel::all());
    }

    /** @test */
    public function can_only_process_specified_fields()
    {
        $path = __DIR__ . '/Support/testfiles/test_extra_columns_import.csv';

        TestModel::csv($path)
            ->only('first_name', 'last_name', 'email', 'contact_number')
            ->import();
        
        $this->assertCount(1, TestModel::all());
    }

    /** @test */
    public function can_format_fields()
    {
        $path = __DIR__ . '/Support/testfiles/test_import.csv';

        TestModel::csv($path)
            ->format('first_name', function($value) {
                return strtoupper($value);
            })
            ->import();

        $this->assertSame('JOE', TestModel::first()->first_name);
    }
}
