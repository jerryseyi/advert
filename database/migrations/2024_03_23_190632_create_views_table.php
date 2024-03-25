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
            $table->string('count');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('views');
    }
};
