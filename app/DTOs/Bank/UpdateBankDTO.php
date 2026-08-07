<?php

namespace App\DTOs\Bank;

use App\Http\Requests\Bank\UpdateRequest;

class UpdateBankDTO
{
    public function __construct(
        public string $id,
        public ?string $bank_name,
        public ?string $short_name,
        public ?string $country_prefix,
        public ?int $bank_prefix, 
    ) {}

    public static function makeFromRequest(UpdateRequest $request): self
    {
        return new self(
            $request->id,
            $request->bank_name,
            $request->short_name,
            $request->country_prefix,
            $request->bank_prefix, 
        );
    }
}
