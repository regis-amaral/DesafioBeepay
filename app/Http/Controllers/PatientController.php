<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientStoreRequest;
use App\Http\Requests\PatientUpdateRequest;
use App\Http\Resources\PatientCollection;
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
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'search_' . md5(json_encode($request->all()));

        // armazena a busca em cache por 10 min
        $patients = Cache::remember($cacheKey, 600, function () use ($request) {
            return Patient::paginate($request->per_page ?? 10);
        });

        if ($patients->isEmpty()) {
            return response()->json(['error' => 'Nenhum paciente encontrado.'], 404);
        }
        return response()->json(new PatientCollection($patients));
    }

    /**
     * Create a newly created resource in storage.
     *
     * @param PatientStoreRequest $request
     * @return JsonResponse
     */
    public function create(PatientStoreRequest $request): JsonResponse
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
            return Patient::find($id);
        });

        if(!$patient){
            return response()->json(['message' => "Paciente não encontrado."], 404);
        }

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
                return response()->json(['error' => 'Paciente não encontrado.'], 404);
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
    public function delete($id): void
    {
        $patient = Patient::find($id);

        if($patient){
            $patient->delete();
            // Remove o paciente do cache
            Cache::forget('patient_' . $id);
        }

    }

    /**
     * Faz o upload de um arquivo CSV contendo dados de pacientes para processamento assíncrono.
     *
     * Este método permite o envio de um arquivo CSV contendo dados de pacientes para importação no sistema.
     * O arquivo enviado deve estar no formato CSV e conter os dados necessários para a criação de pacientes e seus endereços associados.
     * Após o upload, o sistema enfileira uma job para processar a importação de pacientes de forma assíncrona.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadCsv(Request $request): JsonResponse
    {
        // Verificar se o arquivo foi enviado
        if (!$request->hasFile('csv_file')) {
            return response()->json(['error' => 'Nenhum arquivo foi enviado.'], 400);
        }

        // Verificar se o arquivo é um CSV válido
        $file = $request->file('csv_file');
        if (!$file->isValid() || !in_array($file->getClientOriginalExtension(), ['csv', 'txt'])) {
            return response()->json(['error' => 'O arquivo enviado não é um CSV válido.'], 400);
        }

        // Verificar o tamanho do arquivo
        if ($file->getSize() > 10 * 1024 * 1024) { // 10MB
            return response()->json(['error' => 'O tamanho do arquivo excede o limite máximo permitido.'], 400);
        }


        // Salva o arquivo CSV e obtém o caminho do arquivo
        $filePath = $request->file('csv_file')->store('csv_files');

        // Enfileirar a job para processar a importação do CSV
        ProcessPatientsCsvJob::dispatch($filePath);

        return response()->json(['message' => 'Arquivo CSV enviado com sucesso. A importação está em andamento.']);
    }

    /**
     * Pesquisa por pacientes com base em vários critérios.
     *
     * Este método pesquisa pacientes com base em critérios como nome completo, nome da mãe, CPF, CNS e endereço.
     * Regras:
     * 1) Se CPF ou CNS for informado pesquisa apenas com base em um desses campos, ignorando os demais parametros;
     * 2) Busca pelo nome do paciente e/ou nome da mãe do paciente, aceitando nome parcial na busca;
     * 3) Busca pelo endereço com base em todos os parametros de endereço informados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $cacheKey = 'search_' . md5(json_encode($request->all()));

        // armazena a busca em cache por 10 min
        $patients = Cache::remember($cacheKey, 600, function () use ($request) {
            return Patient::where(function ($query) use ($request) {
                // Retorna por CPF ou CNS caso informado
                if(isset($request->cpf) || isset($request->cns)){
                    if (isset($request->cpf)) {
                        $query->orWhere('cpf', $request->cpf);
                    }
                    if (isset($request->cns)) {
                        $query->orWhere('cns', $request->cns);
                    }
                }else{
                    // Se não busca pelo nome do paciente, nome da mãe do paciente e/ou endereço
                    if (isset($request->full_name)) {
                        $query->where('full_name', 'LIKE', '%' . $request->full_name . '%');
                    }

                    if (isset($request->mother_name)) {
                        $query->where('mother_name', 'LIKE', '%' . $request->mother_name . '%');
                    }

                    // A busca por endereço deve corresponder a todos os campos que forem informados
                    if(count(array_intersect_key($request->keys(), ['cep', 'street', 'number', 'neighborhood', 'city', 'state'])) > 0){
                        $query->whereHas('address', function ($query) use ($request){
                            if (isset($request->cep)) {
                                $query->where('addresses.cep', $request->cep);
                            }
                            if (isset($request->street)) {
                                $query->where('addresses.street', 'LIKE', '%' . $request->street . '%');
                            }
                            if (isset($request->number)) {
                                $query->where('addresses.number', $request->number);
                            }
                            if (isset($request->neighborhood)) {
                                $query->where('addresses.neighborhood', 'LIKE', '%' . $request->neighborhood . '%');
                            }
                            if (isset($request->city)) {
                                $query->where('addresses.city', 'LIKE', '%' . $request->city . '%');
                            }
                            if (isset($request->state)) {
                                $query->where('addresses.state', $request->state);
                            }
                        });
                    }
                }
            })->paginate($request->per_page ?? 10);
        });

        if ($patients->isEmpty()) {
            return response()->json(['error' => 'Nenhum paciente encontrado.'], 404);
        }

        return response()->json(new PatientCollection($patients));
    }
}
