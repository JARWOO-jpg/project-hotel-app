<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->enum('type', ['superior', 'deluxe', 'junior_suite', 'presidential_suite']);
            $table->decimal('price_per_night', 12, 2);
            $table->integer('capacity')->default(2);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->json('amenities')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
