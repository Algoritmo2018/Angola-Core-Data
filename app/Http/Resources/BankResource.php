<?php

namespace App\Http\Resources;

use App\Helpers\JwtExtern;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => method_exists($this->resource, 'statu') ? optional($this->resource->statu)->code : null,
            'bank_name' => $this->bank_name,
            'short_name' => $this->short_name,
            'country_prefix' => $this->country_prefix,
            'bank_prefix' => $this->bank_prefix,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
