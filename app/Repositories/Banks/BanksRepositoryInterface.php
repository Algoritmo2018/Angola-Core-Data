<?php

namespace App\Repositories\Banks;

use App\DTOs\Bank\CreateBankDTO;
use App\DTOs\Bank\DeleteBankDTO;
use App\DTOs\Bank\UpdateBankDTO;
use App\Repositories\PaginationInterface;

interface BanksRepositoryInterface
{
    public function get(int $page, int $totalPerPage);

    public function show(string $id);

    public function store(CreateBankDTO $dto);

    public function update(UpdateBankDTO $dto);

    public function moveTrashBank(DeleteBankDTO $dto);

    public function flatTrashCan(int $page, int $totalPerPage, ?string $bankNameFilter): PaginationInterface;

    public function restoreAllBanksFromRecycleBin();

    public function recoverAnItemFromTheRecycleBin(DeleteBankDTO $dto);

    public function permanentlyDeleteBank(DeleteBankDTO $dto);

    public function index(int $page, int $totalPerPage, ?string $bankNameFilter, ?string $bankIdFilter);
}
