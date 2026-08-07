<?php

namespace App\DTOs\Bank;

use App\Http\Requests\Bank\StoreRequest;

class CreateBankDTO
{
    public function __construct(
        public string $bank_name,
        public string $short_name,
        public string $country_prefix,
        public string $bank_prefix, 
    ) {}

    public static function makeFromRequest(StoreRequest $request): self
    {
        return new self(
            $request->bank_name,
            $request->short_name,
            $request->country_prefix,
            $request->bank_prefix, 
        );
    }
}
