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
        Schema::create('user_activity_import_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_activity_id');
            $table->unsignedInteger('total_records')->default(0);
            $table->unsignedInteger('total_success')->default(0);
            $table->unsignedInteger('total_unchanged')->default(0);
            $table->unsignedInteger('total_failed')->default(0);
            $table->timestamps();

            $table->index('user_activity_id', 'idx_user_activity_id');
            $table->foreign('user_activity_id', 'fk_uair_user_activity_id_ua_id')->references('id')->on('management_tool_history');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activity_import_results');
    }
};
