<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calculation_runs', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 180);
            $table->unsignedInteger('alternative_count');
            $table->unsignedInteger('criterion_count');
            $table->decimal('total_weight', 30, 15);
            $table->string('input_hash', 64)->index();
            $table->string('best_alternative_code', 20)->nullable();
            $table->string('best_alternative_name', 150)->nullable();
            $table->decimal('best_preference', 30, 15)->nullable();
            $table->enum('status', ['processing', 'completed', 'failed'])->default('processing');
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('calculation_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calculation_run_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('source_criterion_id')->nullable()->index();
            $table->string('code', 20);
            $table->string('name', 150);
            $table->string('unit', 100)->nullable();
            $table->enum('type', ['benefit', 'cost']);
            $table->decimal('weight', 30, 15);
            $table->decimal('divisor', 30, 15);
            $table->decimal('positive_ideal', 30, 15);
            $table->decimal('negative_ideal', 30, 15);
            $table->timestamps();
            $table->unique(['calculation_run_id', 'code']);
        });

        Schema::create('calculation_alternatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calculation_run_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('source_alternative_id')->nullable()->index();
            $table->string('code', 20);
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('d_positive', 30, 15);
            $table->decimal('d_negative', 30, 15);
            $table->decimal('preference', 30, 15);
            $table->unsignedInteger('rank');
            $table->string('recommendation_status', 100);
            $table->timestamps();
            $table->unique(['calculation_run_id', 'code']);
            $table->index(['calculation_run_id', 'rank']);
        });

        Schema::create('calculation_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calculation_alternative_id')->constrained()->cascadeOnDelete();
            $table->foreignId('calculation_criterion_id')->constrained()->cascadeOnDelete();
            $table->decimal('x_value', 30, 15);
            $table->decimal('r_value', 30, 15);
            $table->decimal('y_value', 30, 15);
            $table->timestamps();
            $table->unique(['calculation_alternative_id', 'calculation_criterion_id'], 'calc_values_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculation_values');
        Schema::dropIfExists('calculation_alternatives');
        Schema::dropIfExists('calculation_criteria');
        Schema::dropIfExists('calculation_runs');
    }
};
