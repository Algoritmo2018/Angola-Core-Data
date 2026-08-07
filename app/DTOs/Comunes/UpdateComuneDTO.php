<?php

namespace App\DTOs\Comunes;

use App\Http\Requests\Comune\UpdateComuneRequest;

class UpdateComuneDTO
{
    public function __construct(
        public string $id,
        public string $name='', 
        public string $municipality_id='',  
    ) {}

    public static function makeFromRequest(UpdateComuneRequest $request): self
    {
return new self(
    $request->id,
    $request->name ?? '',
    $request->municipality_id ?? '',  
);
    }
}
