<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalculationAlternative extends Model
{
    use HasFactory;

    protected $fillable = [
        'calculation_run_id', 'source_alternative_id', 'code', 'name', 'description',
        'd_positive', 'd_negative', 'preference', 'rank', 'recommendation_status',
    ];

    protected function casts(): array
    {
        return [
            'd_positive' => 'decimal:15',
            'd_negative' => 'decimal:15',
            'preference' => 'decimal:15',
        ];
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(CalculationRun::class, 'calculation_run_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(CalculationValue::class);
    }
}
