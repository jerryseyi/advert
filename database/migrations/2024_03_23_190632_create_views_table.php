<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id');
            $table->foreignId('user_id')->nullable();
            $table->foreignId('customer_id')->nullable();
            $table->foreignId('upload_id')->nullable();
            $table->foreignId('owner_device_id')->nullable();
            $table->string('count');
            $table->string('name');
            $table->string('owners_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('views');
    }
};
