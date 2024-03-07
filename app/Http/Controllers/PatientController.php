<?php

namespace App\Http\Controllers;

use App\Http\Resources\PatientResource;
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $patients = Patient::all();
        return response()->json(PatientResource::collection($patients));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
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

        } catch (Throwable $e) {
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
    public function show($id)
    {
        $patient = Cache::remember('patient_'.$id, 3600, function () use ($id) {
            return Patient::findOrFail($id);
        });

        return response()->json(new PatientResource($patient));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
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
                $address = Address::updateOrCreate(
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
    public function destroy($id)
    {
        $patient = Patient::find($id);

        if($patient){
            $patient->delete();
        }

        // Remove o paciente do cache
        Cache::forget('patient_' . $id);
    }
}
