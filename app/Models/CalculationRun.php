<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalculationRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'alternative_count', 'criterion_count', 'total_weight',
        'input_hash', 'best_alternative_code', 'best_alternative_name',
        'best_preference', 'status', 'processed_by', 'error_message',
    ];

    protected function casts(): array
    {
        return [
            'total_weight' => 'decimal:15',
            'best_preference' => 'decimal:15',
        ];
    }

    public function criteria(): HasMany
    {
        return $this->hasMany(CalculationCriterion::class)->orderBy('id');
    }

    public function alternatives(): HasMany
    {
        return $this->hasMany(CalculationAlternative::class)->orderBy('rank');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
