<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Municipality extends Model
{
    use HasUuids, SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'name',
        'province_id'
    ];
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function comunes()
    {
        return $this->hasMany(Comune::class);
    }
}
