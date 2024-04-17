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
        if (!Schema::hasTable('order_details')) {
            Schema::create('order_details', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->autoIncrement();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->string('shipping_address')->nullable();
                $table->boolean('ship_to_different_address')->default(false);
                $table->longText('order_notes')->nullable();
                $table->string('product')->nullable();
                $table->integer('quantity')->nullable();
                $table->decimal('cart_subtotal')->nullable();
                $table->string('shipping_handling')->nullable();
                $table->decimal('order_total')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('order_details')){
            Schema::dropIfExists('order_details');
        }
    }
};
