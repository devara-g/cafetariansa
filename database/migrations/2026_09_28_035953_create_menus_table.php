<?php

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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stand_id')->constrained('stands')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 170)->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('price'); // In Rupiah (e.g. 12000)
            $table->unsignedInteger('cost_price')->nullable(); // Modal/HPP
            $table->string('image')->nullable();
            $table->integer('stock')->default(-1); // -1 = unlimited, >=0 = available daily quota
            $table->boolean('is_available')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_recommended')->default(false);
            $table->unsignedSmallInteger('preparation_time_minutes')->default(10);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['stand_id', 'is_available']);
            $table->index(['category_id', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
