<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Bank\CreateBankDTO;
use App\DTOs\Bank\DeleteBankDTO;
use App\DTOs\Bank\UpdateBankDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bank\DeleteRequest;
use App\Http\Requests\Bank\StoreRequest;
use App\Http\Requests\Bank\UpdateRequest;
use App\Services\BankService;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function __construct(protected BankService $bankService) {}


    public function index(Request $request)
    {
        $bankAll = $this->bankService->index(
            page: $request->get('page', 1),
            totalPerPage: $request->get('per_page', 15),
            bankNameFilter: $request->bankNameFilter,
            bankIdFilter: $request->bankIdFilter
        );

        return $bankAll;
    }

    public function store(StoreRequest $request)
    {
        $createdBank = $this->bankService->store(CreateBankDTO::makeFromRequest($request));

        return $createdBank;
    }

    public function update(UpdateRequest $request)
    {
        $updatedBank = $this->bankService->update(UpdateBankDTO::makeFromRequest($request));

        return $updatedBank;
    }

    public function moveTrashBank(DeleteRequest $request)
    {
        $moveTrashBankBank = $this->bankService->moveTrashBank(DeleteBankDTO::makeFromRequest($request));

        return $moveTrashBankBank;
    }

    public function destroy(DeleteRequest $request)
    {
        return $this->moveTrashBank($request);
    }

    public function flatTrashCan(Request $request)
    {
        $flatTrashCan = $this->bankService->flatTrashCan(
            page: $request->get('page', 1),
            totalPerPage: $request->get('per_page', 15),
            bankNameFilter: $request->bankNameFilter
        );

        return $flatTrashCan;
    }

    public function recoverAnItemFromTheRecycleBin(DeleteRequest $request)
    {
        return $this->bankService->recoverAnItemFromTheRecycleBin(DeleteBankDTO::makeFromRequest($request));
    }

    public function restoreAllBanksFromRecycleBin()
    {
        return $this->bankService->restoreAllBanksFromRecycleBin();
    }

    public function permanentlyDeleteBank(DeleteRequest $request)
    {
        return $this->bankService->permanentlyDeleteBank(DeleteBankDTO::makeFromRequest($request));
    }
}
