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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // 出品者
            $table->string('name');
            $table->text('description', 255);
            $table->unsignedInteger('price')->length(7);
            $table->string('image_path');
            $table->unsignedTinyInteger('condition'); // 1:良好,2:目立った傷や汚れなし,3:やや傷や汚れあり,4:状態が悪い
            $table->string('brand');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
