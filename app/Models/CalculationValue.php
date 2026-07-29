<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalculationValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'calculation_alternative_id', 'calculation_criterion_id',
        'x_value', 'r_value', 'y_value',
    ];

    protected function casts(): array
    {
        return [
            'x_value' => 'decimal:15',
            'r_value' => 'decimal:15',
            'y_value' => 'decimal:15',
        ];
    }

    public function alternative(): BelongsTo
    {
        return $this->belongsTo(CalculationAlternative::class, 'calculation_alternative_id');
    }

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(CalculationCriterion::class, 'calculation_criterion_id');
    }
}
