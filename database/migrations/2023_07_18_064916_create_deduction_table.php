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
        Schema::create('deductions', function (Blueprint $table) {
            $table->id();
            $table->string('pay_id');
            $table->string('payroll_id');
            $table->string('emp_id');
            $table->string('tax1')->default('0.00');
            $table->string('jo_sss')->default('0.00');
            $table->string('jo_smlf_loan')->default('0.00');
            $table->string('tax2')->default('0.00');
            $table->string('add_sal_diff')->default('0.00');
            $table->string('add_nbc_diff')->default('0.00');
            $table->string('add_step_incre')->default('0.00');
            $table->string('add_less_abs')->default('0.00');
            $table->string('add_less_abs1')->default('0.00');
            $table->string('less_late')->default('0.00');
            $table->string('eml')->default('0.00');
            $table->string('pol_gfal')->default('0.00');
            $table->string('consol')->default('0.00');
            $table->string('ed_asst_mpl')->default('0.00');
            $table->string('loan')->default('0.00');
            $table->string('rlip')->default('0.00');
            $table->string('gfal')->default('0.00');
            $table->string('mpl')->default('0.00');
            $table->string('computer')->default('0.00');
            $table->string('health')->default('0.00');
            $table->string('prem')->default('0.00');
            $table->string('calam_loan')->default('0.00');
            $table->string('mp2')->default('0.00');
            $table->string('house_loan')->default('0.00');
            $table->string('philhealth')->default('0.00');
            $table->string('holding_tax')->default('0.00');
            $table->string('lbp')->default('0.00');
            $table->string('cauyan')->default('0.00');
            $table->string('projects')->default('0.00');
            $table->string('nsca_mpc')->default('0.00');
            $table->string('med_deduction')->default('0.00');
            $table->string('grad_guarantor')->default('0.00');
            $table->string('cfi')->default('0.00');
            $table->string('csb')->default('0.00');
            $table->string('fasfeed')->default('0.00');
            $table->string('dis_unliquidated')->default('0.00');
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
        Schema::dropIfExists('deductions');
    }
};
