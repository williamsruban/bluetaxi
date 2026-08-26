<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Foreign Key
            $table->string('new_email')->unique();
            $table->string('old_email')->nullable();
            $table->string('username')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('contact_number')->nullable();
            $table->text('address')->nullable();
            $table->string('home_location')->nullable();
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
};
