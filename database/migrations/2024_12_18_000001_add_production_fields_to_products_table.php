<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->unique()->after('name');
            $table->string('barcode')->nullable()->unique()->after('sku');
            $table->boolean('is_active')->default(true)->after('image');
            $table->integer('min_stock_level')->default(10)->after('count');
            $table->text('notes')->nullable()->after('description');
            
            // Add indexes for better performance
            $table->index(['category_id', 'is_active']);
            $table->index('sku');
            $table->index('barcode');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category_id', 'is_active']);
            $table->dropIndex(['sku']);
            $table->dropIndex(['barcode']);
            
            $table->dropColumn([
                'sku', 'barcode', 'is_active', 
                'min_stock_level', 'notes'
            ]);
        });
    }
};