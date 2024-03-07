<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->nullable(false);
            $table->string('mother_name')->nullable(false);
            $table->date('date_of_birth')->nullable(false);
            $table->string('cpf', 14)->nullable(false)->unique();
            $table->string('cns', 15)->nullable(false)->unique();
            $table->string('photo')->nullable();
            $table->unsignedBigInteger('address_id')->nullable(false);
            $table->timestamps();

            // Define a foreign key constraint
            $table->foreign('address_id')->references('id')->on('addresses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patients');
    }
};
