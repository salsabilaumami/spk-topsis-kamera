<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alternatives', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable()->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('criteria', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable()->unique();
            $table->string('name', 150);
            $table->string('unit', 100)->nullable();
            $table->enum('type', ['benefit', 'cost']);
            $table->decimal('weight', 30, 15);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alternative_id')->constrained()->cascadeOnDelete();
            $table->foreignId('criterion_id')->constrained('criteria')->cascadeOnDelete();
            $table->decimal('value', 30, 15);
            $table->timestamps();
            $table->unique(['alternative_id', 'criterion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('criteria');
        Schema::dropIfExists('alternatives');
    }
};
