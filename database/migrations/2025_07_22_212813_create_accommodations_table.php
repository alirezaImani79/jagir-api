<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('province_id')->nullable();
            $table->foreign('province_id')
                ->references('id')
                ->on('iran_provinces')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedInteger('city_id')->nullable();
            $table->foreign('city_id')
                ->references('id')
                ->on('iran_cities')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignIdFor(User::class, 'owner_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->integer('price_per_day');
            $table->geography('location')->nullable();
            $table->text('address');
            $table->text('description')->nullable();
            $table->json('specifications')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accommodations');
    }
};
