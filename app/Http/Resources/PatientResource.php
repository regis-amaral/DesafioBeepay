<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
class PatientResource extends JsonResource
{

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|Arrayable|\JsonSerializable
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
            'address' => (new AddressResource($this->address)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'links' => [
                'show' => route('patients.show', ['patient' => $this->id]),
                'delete' => route('patients.destroy', ['patient' => $this->id]),
                'update' => route('patients.update', ['patient' => $this->id])
            ],
        ];
    }
}
