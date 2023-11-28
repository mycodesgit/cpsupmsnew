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
        Schema::create('deduction_partime_jos', function (Blueprint $table) {
            $table->id();
            $table->string('pay_id');
            $table->string('payroll_id');
            $table->string('emp_id');
            $table->string('tax1')->default('0.00');
            $table->string('jo_sss')->default('0.00');
            $table->string('tax2')->default('0.00');
            $table->string('add_less_abs')->default('0.00');
            $table->string('less_late')->default('0.00');
            $table->string('projects')->default('0.00');
            $table->string('nsca_mpc')->default('0.00');
            $table->string('grad_guarantor')->default('0.00');
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
        Schema::dropIfExists('deduction_partime_jos');
    }
};
