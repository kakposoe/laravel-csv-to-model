<?php

namespace Kakposoe\CsvToModel;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class CsvToModel
{
    /** @var */
    protected $path;

    /** @var */
    protected $class;

    /** @var */
    protected $fields = [];

    /** @var */
    protected $headers = [];

    /** @var */
    protected $only = [];

    /** @var */
    protected $remove = [];

    public function __construct($path, $class)
    {
        $this->path  = $path;
        $this->class = $class;
    }

    public function headers(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    public function only()
    {
        $this->only = collect(func_get_args());

        return $this;
    }

    public function import()
    {
        $reader = ReaderEntityFactory::createReaderFromFile($this->path);

        $reader->setFieldDelimiter(',');

        $reader->open($this->path);

        collect($reader->getSheetIterator())->each(function ($sheet, $key) {
            collect($sheet->getRowIterator())->each(function ($row, $key) {
                if ($key == 1) {
                    $this->setHeaders($row);
                    return;
                }

                $data = $this->getValues($row);

                if (! empty($this->only)) {
                    $data = $data->reject(function ($value, $key) {
                        return in_array($key ,$this->remove);
                    })->values();
                }

                $data = $data->flatMap(function ($value, $key) {
                    return [$this->fields[$key] => $value];
                })->all();

                $this->class::create($data);
            });
        });

        $reader->close();
    }

    protected function getValues($row)
    {
        return collect($row->getCells())->map(function ($field, $key) {
            return $field->getValue();
        });
    }

    protected function setHeaders($row)
    {
        $this->fields = $this->getValues($row)->map(function ($value, $key) {
            return trim(str_replace(' ', '_', strtolower($value)));
        })->all();

        if (! empty($this->headers)) {
            collect($this->headers)->each(function ($value, $key) {
                $key   = trim(str_replace(' ', '_', strtolower($key)));
                $index = collect($this->fields)->search($key);
                $this->fields[$index] = $value;
            });
        }

        if (! empty($this->only)) {
            $this->fields = collect($this->fields)->reject(function ($value, $key) {
                if (! $index = $this->only->contains($value)) {
                    $this->remove[] = $key;
                    return true;
                }
            })->values()->all();
        }
    }
}
