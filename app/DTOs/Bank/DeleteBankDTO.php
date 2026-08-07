<?php

namespace App\DTOs\Bank;

use App\Http\Requests\Bank\DeleteRequest;

class DeleteBankDTO
{
    public function __construct(
        public string $id,
    ) {}

    public static function makeFromRequest(DeleteRequest $request): self
    {
        return new self(
            $request->id,
        );
    }
}
