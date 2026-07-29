<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalculationCriterion extends Model
{
    use HasFactory;

    protected $fillable = [
        'calculation_run_id', 'source_criterion_id', 'code', 'name', 'unit',
        'type', 'weight', 'divisor', 'positive_ideal', 'negative_ideal',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:15',
            'divisor' => 'decimal:15',
            'positive_ideal' => 'decimal:15',
            'negative_ideal' => 'decimal:15',
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
