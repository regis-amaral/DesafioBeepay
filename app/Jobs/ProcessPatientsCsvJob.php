<?php

namespace App\Jobs;

use App\Models\Address;
use App\Models\Patient;
use App\Services\CsvToJsonService;
use App\Services\PatientValidationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessPatientsCsvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    /**
     * Create a new job instance.
     *
     * @param string $filePath
     * @return void
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Converte o arquivo CSV para um array JSON
        $jsonData = CsvToJsonService::convertPatientsList(storage_path('app/' . $this->filePath));

        $importLog = [];

        DB::beginTransaction();
        foreach ($jsonData as $data) {

            // Validate patient data
            try {
                $validatedData = PatientValidationService::validateData($data);
            } catch (\InvalidArgumentException $e) {
                // Handle validation errors
                $importLog[] = [
                    'full_name' => $data['full_name'],
                    'cpf' => isset($data['cpf']) ? $data['cpf'] : '',
                    'errors' => $e->getMessage(),
                ];
                continue; // Skip to the next row
            }

            // Save patient data to the database
            $patient = new Patient($validatedData);
            $patient->save();

            $address = new Address();
            $address->fill($validatedData['address']);
            $address->patient_id = $patient->id;
            $address->save();

        }
        if (empty($importLog)) {
            DB::commit();
        } else {
            DB::rollback();
            $this->failed();
        }

        // Save import log
        $this->saveImportLog($importLog);
    }

    /**
     * Save import log.
     *
     * @param array $importLog
     * @return void
     */
    private function saveImportLog(array $importLog)
    {
        $logContent = '';
        foreach ($importLog as $log) {
            $logContent .= "Name: {$log['full_name']}, CPF: {$log['cpf']}, Errors: {$log['errors']}\n";
        }

        Log::channel('import')->info($logContent);
    }
}
