<?php

namespace App\DTOs\Municipality;

use App\Http\Requests\Municipality\UpdateMunicipalityRequest;

class UpdateMunicipalityDTO
{
    public function __construct(
        public string $id,
        public string $name='', 
        public string $province_id='',  
    ) {}

    public static function makeFromRequest(UpdateMunicipalityRequest $request): self
    {
return new self(
    $request->id,
    $request->name ?? '',
    $request->province_id ?? '',  
);
    }
}
