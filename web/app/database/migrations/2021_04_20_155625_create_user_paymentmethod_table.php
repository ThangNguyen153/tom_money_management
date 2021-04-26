<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPaymentmethodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_paymentmethod', function (Blueprint $table) {
            $table->bigInteger('user_id');
            $table->bigInteger('paymentmethod_id');
            $table->double('amount')->default(0);
            $table->primary(['user_id', 'paymentmethod_id']);
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_paymentmethod');
    }
}
