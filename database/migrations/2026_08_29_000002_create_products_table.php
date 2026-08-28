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
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->decimal('regular_price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock_qty')->default(25);
            $table->string('thumbnail');
            $table->json('gallery')->nullable();
            $table->json('sizes')->nullable(); // e.g. ["M", "L", "XL", "XXL"] or ["30", "32", "34", "36"]
            $table->json('colors')->nullable();
            $table->text('short_desc')->nullable();
            $table->longText('description')->nullable();
            $table->text('size_chart_html')->nullable();
            $table->string('badge')->nullable(); // e.g. '25% OFF', 'Eid Special', 'Hot Deal', 'New In'
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->integer('reviews_count')->default(12);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
