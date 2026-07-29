<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Criterion extends Model
{
    use HasFactory;

    protected $table = 'criteria';
    protected $fillable = ['name', 'unit', 'type', 'weight', 'description'];

    protected function casts(): array
    {
        return ['weight' => 'decimal:15'];
    }

    protected static function booted(): void
    {
        static::created(function (Criterion $criterion): void {
            if ($criterion->code === null) {
                $criterion->forceFill(['code' => 'C'.$criterion->getKey()])->saveQuietly();
            }
        });
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}
