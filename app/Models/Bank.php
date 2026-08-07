<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasUuids, SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'bank_name',
        'short_name',
        'country_prefix',
        'bank_prefix',
    ];
}
