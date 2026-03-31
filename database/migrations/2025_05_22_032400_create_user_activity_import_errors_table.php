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
        Schema::create('user_activity_import_errors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_activity_import_result_id');
            $table->string('identify_name', 255)->nullable();
            $table->string('identify_value', 255)->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('user_activity_import_result_id', 'idx_user_activity_import_result_id');
            $table->foreign('user_activity_import_result_id', 'fk_uaie_user_activity_import_result_id_uair_id')->references('id')->on('user_activity_import_results');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activity_import_errors');
    }
};
