<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alternative extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    protected static function booted(): void
    {
        static::created(function (Alternative $alternative): void {
            if ($alternative->code === null) {
                $alternative->forceFill(['code' => 'A'.$alternative->getKey()])->saveQuietly();
            }
        });
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}
