<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')
                    ->references('id')->on('suppliers')
                    ->onDelete('set null');
            $table->unsignedBigInteger('product_category_id');
            $table->foreign('product_category_id')
                    ->references('id')->on('product_categories')
                    ->onDelete('restrict');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->decimal('costs', 10, 2);
            $table->decimal('wholesale_price', 10, 2);
            $table->decimal('retail_price', 10, 2);
            $table->timestamps();

            $table->index('supplier_id');
            $table->index('name');
            $table->index('wholesale_price');
            $table->index('retail_price');
            $table->index('product_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
