<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Province extends Model
{
    use HasUuids, SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'name',
    ];
    public function municipalities()
    {
        return $this->hasMany(Municipality::class);
    }
}
