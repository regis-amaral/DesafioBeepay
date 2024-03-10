<?php

namespace App\Services;

class CsvToJsonService
{
    public static function convertPatientsList($filename = '', $delimiter = ','):array|false
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            throw new \RuntimeException("O arquivo '{$filename}' não pode ser aberto para leitura.");
        }

        $header = null;
        $data = [];

        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    if (empty($row)) {
                        fclose($handle);
                        throw new \InvalidArgumentException("O arquivo está vazio.");
                    }
                    $rowData = [];
                    foreach ($row as $index => $value) {
                        if (isset($header[$index])) { // Verificar se o índice existe no cabeçalho
                            $key = $header[$index];
                            if (strpos($key, 'address.') === 0) {
                                $addressKey = str_replace('address.', '', $key);
                                $rowData['address'][$addressKey] = $value;
                            } else {
                                $rowData[$key] = $value;
                            }
                        }else{
                            throw new \RuntimeException("O arquivo possui erro de formatação de dados.");
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
