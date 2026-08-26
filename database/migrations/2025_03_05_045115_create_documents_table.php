<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('rc_book')->nullable();
            $table->string('road_tax')->nullable();
            $table->string('driving_licence')->nullable();
            $table->string('permit')->nullable();
            $table->string('drivers_batch')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('documents');
    }
};
