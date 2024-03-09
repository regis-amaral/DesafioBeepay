<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Str;

class PatientSearchCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $query = $request->query();

        $firstPageUrl = $this->url(1);
        $lastPageUrl = $this->url($this->lastPage());


        foreach ($query as $key => $value) {
            if ($key !== 'page') {
                $firstPageUrl = Str::contains($firstPageUrl, '?') ? $firstPageUrl . '&' . $key . '=' . $value : $firstPageUrl . '?' . $key . '=' . $value;
                $lastPageUrl = Str::contains($lastPageUrl, '?') ? $lastPageUrl . '&' . $key . '=' . $value : $lastPageUrl . '?' . $key . '=' . $value;
            }
        }

        $prevPageUrl = $this->previousPageUrl();
        if ($prevPageUrl) {
            foreach ($query as $key => $value) {
                if ($key !== 'page') {
                    $prevPageUrl = Str::contains($prevPageUrl, '?') ? $prevPageUrl . '&' . $key . '=' . $value : $prevPageUrl . '?' . $key . '=' . $value;
                }
            }
        }

        $nextPageUrl = $this->nextPageUrl();
        if ($nextPageUrl) {
            foreach ($query as $key => $value) {
                if ($key !== 'page') {
                    $nextPageUrl = Str::contains($nextPageUrl, '?') ? $nextPageUrl . '&' . $key . '=' . $value : $nextPageUrl . '?' . $key . '=' . $value;
                }
            }
        }

        return [
            'data' => $this->collection,
            'links' => [
                'first' => $firstPageUrl,
                'last' => $lastPageUrl,
                'prev' => $prevPageUrl,
                'next' => $nextPageUrl,
            ],
            'meta' => [
                'current_page' => $this->currentPage(),
                'from' => $this->firstItem(),
                'last_page' => $this->lastPage(),
                'path' => $this->path(),
                'per_page' => $this->perPage(),
                'to' => $this->lastItem(),
                'total' => $this->total(),
            ],
        ];
    }
}
