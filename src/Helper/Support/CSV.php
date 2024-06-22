<?php

namespace Weirdo\Helper\Support;

use Weirdo\Helper\Helper;

class CSV
{
    use Helper;

    /**
     * @var string
     */
    protected $seederPath;

    /**
     * @param string $seederPath
     */
    public function __construct($seederPath = 'database/seeders/csv')
    {
        $this->seederPath = $seederPath;
    }

    /**
     * @param string $archivo
     * @param boolean $encoding
     * @return array
     */
    public function ToArrayCsv($archivo, $encoding = false)
    {
        $path = base_path($this->seederPath);

        $file = file("{$path}/{$archivo}");
        $csv = array_map('str_getcsv', $file);
        if ($encoding) {
            $csv = $this->setHtmlentities($csv);
        }

        array_walk($csv, function (&$val) use ($csv) {
            $val = array_combine($csv[0], $val);
        });

        return array_slice($csv, 1, count($csv));
    }

    /**
     * @param string $archivo
     * @param boolean $encoding
     * @return array
     */
    public function ToArrayCsvEfficient($archivo, $encoding = false)
    {
        $path = base_path($this->seederPath);
        $handle = fopen("{$path}/{$archivo}", 'r');
        if ($handle === false) {
            return [];
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            return [];
        }

        $lines = [];
        while (($line = fgetcsv($handle)) !== FALSE) {
            $lines[] =  array_combine($header, $line);
        }
        if ($encoding) {
            $lines = $this->setHtmlentities($lines);
        }

        fclose($handle);

        return $lines;
    }
}