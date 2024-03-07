<?php

namespace App\Http\Controllers;

use App\Http\Resources\PatientCollection;
use App\Http\Resources\PatientResource;
use App\Models\Address;
use App\Models\Patient;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        DB::beginTransaction();
        try{
            $patient = new Patient();
            $patient->full_name = $request->full_name;
            $patient->mother_name = $request->mother_name;
            $patient->date_of_birth = $request->date_of_birth;
            $patient->cpf = $request->cpf;
            $patient->cns = $request->cns;

            $address = new Address();
            $address->cep = $request->address['cep'];
            $address->address = $request->address['address'];
            $address->number = $request->address['number'];
            $address->complement = $request->address['complement'];
            $address->neighborhood = $request->address['neighborhood'];
            $address->city = $request->address['city'];
            $address->state = $request->address['state'];

            $address->save();
            $patient->address_id = $address->id;
            $patient->save();

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
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json(['error' => 'Paciente não encontrado'], 404);
        }

        return response()->json(new PatientResource($patient));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try{
            $patient = Patient::find($id);
            $patient->full_name = $request->full_name;
            $patient->mother_name = $request->mother_name;
            $patient->date_of_birth = $request->date_of_birth;
            $patient->cpf = $request->cpf;
            $patient->cns = $request->cns;
            $patient->save();

            if(isset($request->address)){
                $address = Address::find($patient->address_id);
                $address->cep = $request->address['cep'];
                $address->address = $request->address['address'];
                $address->number = $request->address['number'];
                $address->complement = $request->address['complement'];
                $address->neighborhood = $request->address['neighborhood'];
                $address->city = $request->address['city'];
                $address->state = $request->address['state'];
                $address->save();
            }

            DB::commit();
            return response()->json(['message' => 'Paciente atualizado com sucesso', 'data' => new PatientResource($patient)], 201);

        } catch (Throwable $e) {
            DB::rollback();
            return response()->json(['message' => 'Erro ao atualizar paciente: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $patient = Patient::find($id);
        $patient->delete();
    }
}
