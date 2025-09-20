<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('tax_amount', 10, 2)->default(0)->after('totalPrice');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('tax_amount');
            $table->timestamp('delivered_at')->nullable()->after('reject_reason');
            $table->text('notes')->nullable()->after('delivered_at');
            
            // Add indexes for better performance
            $table->index(['status', 'created_at']);
            $table->index('order_code');
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['order_code']);
            $table->dropIndex(['user_id', 'status']);
            
            $table->dropColumn([
                'tax_amount', 'discount_amount', 
                'delivered_at', 'notes'
            ]);
        });
    }
};