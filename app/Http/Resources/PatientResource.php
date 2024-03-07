<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\AddressResource;
class PatientResource extends JsonResource
{

    /**
     * Transform the resource collection into an array.
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
            'address' => (new AddressResource($this->address)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
