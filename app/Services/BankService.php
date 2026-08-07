<?php

namespace App\Services;

use App\Adapters\ApiAdapter;
use App\DTOs\Bank\CreateBankDTO;
use App\DTOs\Bank\DeleteBankDTO;
use App\DTOs\Bank\UpdateBankDTO;
use App\Http\Resources\BankResource;
use App\Repositories\Banks\BanksRepositoryInterface;

class BankService
{
    public function __construct(protected BanksRepositoryInterface $banksRepositoryInterface) {}

    public function store(CreateBankDTO $dto)
    {
        $bank = $this->banksRepositoryInterface->store($dto);

        return response()->json([
            'success' => true,
            'message' => 'Banco cadastrado com sucesso',
            'data' => new BankResource($bank),
        ], 201);
    }

    public function update(UpdateBankDTO $dto)
    {
        $bank = $this->banksRepositoryInterface->update($dto);

        return response()->json([
            'success' => true,
            'message' => 'Dados do banco atualizado com sucesso',
            'data' => new BankResource($bank),
        ], 200);
    }

    public function moveTrashBank(DeleteBankDTO $dto)
    {
        $this->banksRepositoryInterface->moveTrashBank($dto);

        return response()->json([
            'success' => true,
            'message' => 'Banco movido para a lixeira',
        ], 200);
    }

    public function flatTrashCan(int $page, int $totalPerPage, ?string $bankNameFilter)
    {
        $moveTrashPlan = $this->banksRepositoryInterface->flatTrashCan(
            page: $page,
            totalPerPage: $totalPerPage,
            bankNameFilter: $bankNameFilter
        );

        return ApiAdapter::toJson($moveTrashPlan);
    }

    public function restoreAllBanksFromRecycleBin()
    {
        $restoreAllBanksFromRecycleBin = $this->banksRepositoryInterface->restoreAllBanksFromRecycleBin();

        return response()->json([
            'success' => true,
            'message' => 'Bancos recuperados da lixeira',
        ], 200);
    }

    public function recoverAnItemFromTheRecycleBin(DeleteBankDTO $dto)
    {
        $recoverAnItemFromTheRecycleBin = $this->banksRepositoryInterface->recoverAnItemFromTheRecycleBin($dto);

        return response()->json([
            'success' => true,
            'message' => 'Banco recuperado com sucesso',
        ], 200);
    }

    public function permanentlyDeleteBank(DeleteBankDTO $dto)
    {
        $permanentlyDeleteBank = $this->banksRepositoryInterface->permanentlyDeleteBank($dto);

        return response()->json([
            'success' => true,
            'message' => 'Banco deletado permanentemente com sucesso',
        ], 200);
    }

    public function index(int $page, int $totalPerPage, ?string $bankNameFilter, ?string $bankIdFilter)
    {
        $bankLists = $this->banksRepositoryInterface->index(
            page: $page,
            totalPerPage: $totalPerPage,
            bankNameFilter: $bankNameFilter,
            bankIdFilter: $bankIdFilter,
        );

        return ApiAdapter::toJson($bankLists);
    }
}
