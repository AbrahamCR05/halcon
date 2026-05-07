<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('eliminado');
            $table->string('route_photo')->nullable()->after('created_by');
            $table->string('delivery_photo')->nullable()->after('route_photo');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'route_photo', 'delivery_photo']);
        });
    }
};