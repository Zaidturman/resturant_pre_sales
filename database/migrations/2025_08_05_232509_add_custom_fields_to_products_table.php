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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('special_price', 10, 2)->nullable()->after('quantity');
            $table->string('capacity', 50)->nullable()->after('special_price');
            $table->string('weight', 50)->nullable()->after('capacity');
            $table->string('unique_code', 50)->unique()->nullable()->after('weight');

            // يمكنك إضافة فهرس للبحث السريع على الرمز الخاص
            $table->index('unique_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('special_price');
            $table->dropColumn('capacity');
            $table->dropColumn('weight');
            $table->dropColumn('unique_code');

            // حذف الفهرس إذا كان موجوداً
            $table->dropIndex(['unique_code']);
        });
    }
};
