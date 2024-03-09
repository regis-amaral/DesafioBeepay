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
            'options' => [
                'show' => route('show_patient', ['id' => $this->id]),
                'delete' => route('delete_patient', ['id' => $this->id]),
                'update' => route('update_patient', ['id' => $this->id])
            ],
        ];
    }
}
