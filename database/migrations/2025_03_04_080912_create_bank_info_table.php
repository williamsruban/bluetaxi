<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('bank_infos', function (Blueprint $table) {
            $table->id();  // Primary Key
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign Key
            $table->string('bank_name');
            $table->string('ifsc_code', 11);
            $table->string('account_holder_name');
            $table->string('account_number')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bank_infos');
    }
};
