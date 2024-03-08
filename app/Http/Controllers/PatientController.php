<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientStoreRequest;
use App\Http\Requests\PatientUpdateRequest;
use App\Http\Resources\PatientResource;
use App\Jobs\ProcessPatientsCsvJob;
use App\Models\Address;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class PatientController extends Controller
{

    public function welcome(): JsonResponse
    {
        $response['status'] = 'API Online';
        return Response::json($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $patients = Patient::all();
        return response()->json(PatientResource::collection($patients));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PatientStoreRequest $request
     * @return JsonResponse
     */
    public function store(PatientStoreRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try{
            $patient = new Patient();
            $patient->fill($request->all());
            $patient->save();

            $address = new Address();
            $address->fill($request->address);
            $address->patient_id = $patient->id;
            $address->save();

            DB::commit();
            return response()->json(['message' => 'Paciente criado com sucesso', 'data' => new PatientResource($patient)], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Erro ao criar paciente: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $patient = Cache::remember('patient_'.$id, 3600, function () use ($id) {
            return Patient::findOrFail($id);
        });

        return response()->json(new PatientResource($patient));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PatientUpdateRequest $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(PatientUpdateRequest $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try{
            $patient = Patient::find($id);
            if (!$patient) {
                return response()->json(['error' => 'Paciente não encontrado'], 404);
            }
            $patient->fill($request->all());
            $patient->save();

            if (isset($request->address)) {
                Address::updateOrCreate(
                    ['patient_id' => $patient->id],
                    $request->address
                );
            }

            // Atualiza o cache
            Cache::put('patient_' . $patient->id, $patient, 3600);

            DB::commit();
            return response()->json(['message' => 'Paciente atualizado com sucesso', 'data' => new PatientResource($patient)], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Erro ao atualizar paciente: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return void
     */
    public function destroy($id): void
    {
        $patient = Patient::find($id);

        if($patient){
            $patient->delete();
        }

        // Remove o paciente do cache
        Cache::forget('patient_' . $id);
    }

    public function uploadCsv(Request $request): JsonResponse
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048', // Assuming max file size is 2MB
        ]);

        // Salva o arquivo CSV e obtém o caminho do arquivo
        $filePath = $request->file('csv_file')->store('csv_files');

        // Enfileirar a job para processar a importação do CSV
        ProcessPatientsCsvJob::dispatch($filePath);

        return response()->json(['message' => 'Arquivo CSV enviado com sucesso. A importação está em andamento.']);
    }

}
