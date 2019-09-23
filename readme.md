# Laravel Csv To Model
 Helper for Laravel Eloquent to import csv data directly into a model.

[![Build Status](https://travis-ci.org/kakposoe/laravel-csv-to-model.svg?branch=master)](https://travis-ci.org/kakposoe/laravel-csv-to-model)
[![StyleCI](https://styleci.io/repos/202747399/shield?branch=master)](https://styleci.io/repos/202747399)

## Installation

Via Composer

``` bash
$ composer require kakposoe/laravel-csv-to-model
```

## Usage
The package offers an expressive api to prepare and process an entire csv import into the database via an eloquent command.

``` php
$csv = Model::csv($path)
$csv->import();
```

Behind the scenes, the package wraps the [box/spout](https://github.com/box/spout) package to iterate the imported csv.

The first argument expects the file path for the csv file. After this, running `->import()` will import all rows into the database.

### Changing field names
By default, the first row of the csv file will be used to determine the model fields to fill. The field names will be automatically changed to `snake_case`.

If you need to change the field name, you can use the `->headers()` method and pass an array to map the field names:

``` php
$csv->headers(['Email Address' => 'email'])
```

**Note:** If the field name is not in the array passed into the `->headers()` method, it will default to the original, `snake_case` version of the field.

### Specifying fields to import
You can use the `->only()` method to specify what fields should be imported:

``` php
$csv->only('first_name', 'last_name', 'email', 'contact_number')
```

**Note:** If using in conjuction with `->headers()`, this would be the **mapped name**, not the original name.

### Formatting values
It is possible to format a value before each insert by using the `->format()` method:

``` php
$csv->format('first_name', function($value) {
    return strtoupper($value);
})
```

The first argument is the target field (if field name is changed used `->headers()`, use the **mapped name**). The second argument should be a closure, returning the formatted data.

## Credits
- [author name][link-author]
- [All Contributors][link-contributors]
