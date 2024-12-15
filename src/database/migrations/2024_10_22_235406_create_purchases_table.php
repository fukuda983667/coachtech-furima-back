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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // 購入者
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('payment_method');
            $table->string('postal_code')->nullable(); // 郵便番号
            $table->string('address')->nullable(); // 住所
            $table->string('building_name')->nullable(); // 建物名
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
