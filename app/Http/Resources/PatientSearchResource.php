<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'photo' => $this->photo,
            'full_name' => $this->full_name,
            'mother_name' => $this->mother_name,
            'date_of_birth' => $this->date_of_birth,
            'cpf' => $this->cpf,
            'cns' => $this->cns,
            'address' => [
                'id' => $this->address->id,
                'cep' => $this->address->cep,
                'address' => $this->address->address,
                'number' => $this->address->number,
                'complement' => $this->address->complement,
                'neighborhood' => $this->address->neighborhood,
                'city' => $this->address->city,
                'state' => $this->address->state,
                'created_at' => $this->address->created_at,
                'updated_at' => $this->address->updated_at,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
