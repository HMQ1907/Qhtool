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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_template_id')->after('hash')->nullable();
            $table->unsignedBigInteger('created_by')->after('purchase_template_id')->nullable();
            $table->unsignedBigInteger('updated_by')->after('created_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('purchase_template_id');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
};
