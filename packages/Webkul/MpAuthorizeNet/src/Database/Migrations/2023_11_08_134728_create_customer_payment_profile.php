<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_payment_profile', function (Blueprint $table) {

            $table->id();
            $table->integer('profile_id',25);
            $table->integer('payment_profile_id',25);
            $table->integer('customer_id',10)->nullable();
            $table->integer('email',50)->nullable();
            $table->string('customer_token',255)->nullable();
            $table->integer('order_id',10)->nullable();
            $table->string('airport',225);
            $table->string('billing_address',255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_payment_profile');
    }
};
