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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // 購入者
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('status_id')->constrained('order_statuses')->onDelete('cascade'); // 注文状況
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
        Schema::dropIfExists('orders');
    }
};
