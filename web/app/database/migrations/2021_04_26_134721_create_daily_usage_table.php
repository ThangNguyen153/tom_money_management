<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyUsageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('paymentmethod_id');
            $table->unsignedBigInteger('usagetype_id');
            $table->double('paid')->default(0);
            $table->double('extra')->default(0);
            $table->longText('description')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('paymentmethod_id')->references('id')->on('payment_methods');
            $table->foreign('usagetype_id')->references('id')->on('usage_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_usages');
    }
}
