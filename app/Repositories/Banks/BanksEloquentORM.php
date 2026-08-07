<?php

namespace App\Repositories\Banks;

use App\DTOs\Bank\CreateBankDTO;
use App\DTOs\Bank\DeleteBankDTO;
use App\DTOs\Bank\UpdateBankDTO;
use App\Http\Resources\BankResource;
use App\Models\Bank;
use App\Repositories\PaginationInterface;
use App\Repositories\PaginationPresenter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class BanksEloquentORM implements BanksRepositoryInterface
{
    public function get(int $page = 1, int $totalPerPage = 15)
    {
        // 🔹 Última atualização registrada
        $lastUpdate = Cache::get('banks:last_update', now());

        // 🔹 Gerar uma chave de cache única por página + última atualização
        $cacheKey = "banks:page={$page}:perPage={$totalPerPage}:updated_at=" . md5($lastUpdate);

        // 🔹 Recuperar ou gerar cache automaticamente
        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($page, $totalPerPage) {
            $banks = Bank::orderBy('created_at', 'desc')
                ->paginate($totalPerPage, ['*'], 'page', $page);

            $presenter = new PaginationPresenter($banks);
            $presenter->items = BankResource::collection($banks->items())->resolve();

            return $presenter;
        });
    }

    public function show(string $id) {}

    public function store(CreateBankDTO $dto)
    {
        return Bank::create([
            'bank_name' => $dto->bank_name,
            'short_name' => $dto->short_name,
            'country_prefix' => $dto->country_prefix,
            'bank_prefix' => $dto->bank_prefix,
        ]);
    }

    public function update(UpdateBankDTO $dto)
    {
        $bank = Bank::findOrFail($dto->id);

        if ($dto->bank_name !== null) {
            $bank->bank_name = $dto->bank_name;
        }
        if ($dto->short_name !== null) {
            $bank->short_name = $dto->short_name;
        }
        if ($dto->country_prefix !== null) {
            $bank->country_prefix = $dto->country_prefix;
        }
        if ($dto->bank_prefix !== null) {
            $bank->bank_prefix = $dto->bank_prefix;
        }

        $bank->save();

        return $bank;
    }

    public function moveTrashBank(DeleteBankDTO $dto)
    {
        Bank::where('id', $dto->id)->delete();
    }

    public function flatTrashCan(int $page, int $totalPerPage, ?string $bankNameFilter): PaginationInterface
    {
        $flatTrashCan = Bank::where('deleted_at', '<>', null)
            ->where(function ($query) use ($bankNameFilter) {
                if ($bankNameFilter) {
                    $query->where('bank_name', 'like', "%{$bankNameFilter}%");
                }
            })
            ->onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate($totalPerPage, ['*'], 'page', $page);

        return new PaginationPresenter($flatTrashCan);
    }

    public function restoreAllBanksFromRecycleBin()
    {
        Bank::onlyTrashed()->restore();
    }

    public function recoverAnItemFromTheRecycleBin(DeleteBankDTO $dto)
    {
        $recoverAnItemFromTheRecycleBin = Bank::withTrashed()->find($dto->id);
        $recoverAnItemFromTheRecycleBin->restore();
    }

    public function permanentlyDeleteBank(DeleteBankDTO $dto)
    {
        Bank::withTrashed()->find($dto->id)->forceDelete();
    }

    public function index(int $page, int $totalPerPage, ?string $bankNameFilter, ?string $bankIdFilter)
    {
        $bankLists = Bank::where(function ($query) use ($bankNameFilter) {
            if ($bankNameFilter) {
                $query->where('bank_name', 'like', "%{$bankNameFilter}%");
            }
        })->where(function ($query) use ($bankIdFilter) {
            if ($bankIdFilter) {
                $query->where('id', $bankIdFilter);
            }
        })->where('deleted_at', null)
            ->orderBy('created_at', 'desc')
            ->paginate($totalPerPage, ['*'], 'page', $page);

        return new PaginationPresenter($bankLists);
    }
}
