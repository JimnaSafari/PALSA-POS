<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->text('description')->nullable()->after('account_name');
            $table->boolean('is_active')->default(true)->after('description');
            $table->string('network')->nullable()->after('is_active');
            $table->string('ussd_code')->nullable()->after('network');
            $table->decimal('min_amount', 10, 2)->default(1)->after('ussd_code');
            $table->decimal('max_amount', 10, 2)->default(1000000)->after('min_amount');
            $table->decimal('fee_percentage', 5, 2)->default(0)->after('max_amount');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'description', 'is_active', 'network', 'ussd_code',
                'min_amount', 'max_amount', 'fee_percentage'
            ]);
        });
    }
};