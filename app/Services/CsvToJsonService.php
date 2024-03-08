<?php

namespace App\Services;

class CsvToJsonService
{
    public static function convertPatientsList($filename = '', $delimiter = ','):array|false
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $header = null;
        $data = [];

        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $rowData = [];
                    foreach ($row as $index => $value) {
                        $key = $header[$index];
                        if (strpos($key, 'address.') === 0) {
                            $addressKey = str_replace('address.', '', $key);
                            $rowData['address'][$addressKey] = $value;
                        } else {
                            $rowData[$key] = $value;
                        }
                    }
                    $data[] = $rowData;
                }
            }
            fclose($handle);
        }

        return $data;
    }
}
