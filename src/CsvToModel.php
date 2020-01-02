<?php

namespace Kakposoe\CsvToModel;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Support\Str;

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
    protected $concat = [];

    /** @var */
    protected $additions = [];

    /** @var */
    protected $ignoreDuplicates = [];

    /** @var */
    protected $remove = [];

    /** @var */
    protected $format = [];

    public function __construct($path, $class)
    {
        $this->path = $path;
        $this->class = $class;
    }

    public function headers(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    public function concat(array $concat)
    {
        $this->concat = $concat;

        return $this;
    }

    public function additions(array $additions)
    {
        $this->additions = $additions;

        return $this;
    }

    public function ignoreDuplicates()
    {
        $this->ignoreDuplicates = collect(func_get_args());

        return $this;
    }

    public function format($field, $closure)
    {
        $this->format[$field] = $closure;

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

        if (pathinfo($this->path)['extension'] == 'csx') {
            $reader->setFieldDelimiter(',');
        }

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
                        return in_array($key, $this->remove);
                    })->values();
                }

                if (! empty($this->format)) {
                    $data = $this->formatData($data);
                }

                $data = $data->flatMap(function ($value, $key) {
                    return [$this->fields[$key] => $value];
                })->all();

                if (! empty($this->concat)) {
                    foreach ($this->concat as $field => $fields) {
                        $new = [];

                        foreach ($fields as $key => $value) {
                            $new[$key] = $data[$value];
                            unset($data[$value]);
                        }

                        $data[$field] = implode(' ', $new);
                    }
                }

                if (! empty($this->additions)) {
                    foreach ($this->additions as $key => $value) {
                        $data[$key] = $value;
                    }
                }

                $query = new $this->class;

                if (! empty($this->ignoreDuplicates)) {
                    foreach ($this->ignoreDuplicates as $field) {
                        $query = $query->where($field, $data[$field]);
                    }

                    if ($query->exists()) {
                        return;
                    }
                }

                $this->class::create($data);
            });
        });

        $reader->close();
    }

    /**
     * Get values of that row.
     *
     * @param   \Box\Spout\Common\Entity\Row    $row
     *
     * @return  \Illuminate\Support\Collection
     */
    protected function getValues($row)
    {
        return collect($row->getCells())->map(function ($field, $key) {
            return $field->getValue();
        });
    }

    /**
     * Set database fields using headers.
     *
     * @param   \Box\Spout\Common\Entity\Row    $row
     *
     * @return  void
     */
    protected function setHeaders($row)
    {
        $this->fields = $this->getValues($row)->map(function ($value, $key) {
            return Str::snake($value);
        })->all();

        if (! empty($this->headers)) {
            collect($this->headers)->each(function ($value, $key) {
                $index = collect($this->fields)->search(Str::snake($key));
                $this->fields[$index] = $value;
            });
        }

        if (! empty($this->only)) {
            $this->fields = collect($this->fields)->reject(function ($value, $key) {
                if (! $this->only->contains($value)) {
                    $this->remove[] = $key;
                    return true;
                }
            })->values()->all();
        }
    }

    /**
     * Get values of that row.
     *
     * @param   \Illuminate\Support\Collection    $data
     *
     * @return  \Illuminate\Support\Collection
     */
    protected function formatData($data)
    {
        return $data->map(function ($value, $key) {
            return isset($this->format[$this->fields[$key]])
                ? $this->format[$this->fields[$key]]($value)
                : $value;
        });
    }
}
