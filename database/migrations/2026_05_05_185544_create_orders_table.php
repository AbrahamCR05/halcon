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
        Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('invoice')->unique();
    $table->string('client_num');
    $table->string('client_name');
    $table->string('rfc')->nullable();
    $table->string('phone')->nullable();
    $table->string('address');
    $table->dateTime('order_date');
    $table->text('notes')->nullable();
    $table->enum('status', ['Ordered','In Process','In Route','Delivered'])->default('Ordered');
    $table->string('route_photo')->nullable();
    $table->string('delivery_photo')->nullable();
    $table->softDeletes(); // eliminación lógica
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
