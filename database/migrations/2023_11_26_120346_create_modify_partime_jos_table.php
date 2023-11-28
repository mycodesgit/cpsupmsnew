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
        Schema::create('modify_partime_jos', function (Blueprint $table) {
            $table->id();
            $table->string('pay_id');
            $table->string('payroll_id');
            $table->string('off_id');
            $table->string('column');
            $table->string('affected')->nullable();
            $table->string('label')->nullable();
            $table->string('action');
            $table->string('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modify_partime_jos');
    }
};
