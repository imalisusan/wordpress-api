<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('customer_details')) {
            Schema::create('customer_details', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->autoIncrement();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('company_name')->nullable();
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->boolean('create_account')->default(false);
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('customer_details')) {
            Schema::dropIfExists('customer_details');
        }
    }
};
