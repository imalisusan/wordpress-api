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
        if (!Schema::hasTable('payment_details')) {
            Schema::create('payment_details', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->autoIncrement();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->string('payment_method')->nullable();
                $table->string('paypal_account')->nullable();
                $table->string('bank_account')->nullable();
                $table->string('cheque_details')->nullable();
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('customer_details')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('payment_details')){
            Schema::dropIfExists('payment_details');
        }
    }
};
