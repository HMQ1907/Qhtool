<?php
// cspell:ignore Webike meno
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
        Schema::create('purchase_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('memo')->nullable();
            $table->string('webike_logo_url', 255)->nullable();
            $table->string('webike_name', 255)->nullable();
            $table->string('webike_tax_id', 255)->nullable();
            $table->string('webike_street_address', 255)->nullable();
            $table->string('webike_district_sub_district', 255)->nullable();
            $table->string('webike_city_municipality', 255)->nullable();
            $table->string('webike_state_province', 255)->nullable();
            $table->string('webike_postal_code_zip_code', 255)->nullable();
            $table->string('webike_country', 255)->nullable();
            $table->string('webike_phone_number', 255)->nullable();
            $table->string('ship_to_name', 255)->nullable();
            $table->string('ship_to_street_address', 255)->nullable();
            $table->string('ship_to_district_sub_district', 255)->nullable();
            $table->string('ship_to_city_municipality', 255)->nullable();
            $table->string('ship_to_state_province', 255)->nullable();
            $table->string('ship_to_postal_code_zip_code', 255)->nullable();
            $table->string('ship_to_country', 255)->nullable();
            $table->string('ship_to_phone_number', 255)->nullable();
            $table->json('items')->nullable();
            $table->tinyInteger('is_display_total_amount')->nullable();
            $table->text('note')->nullable();
            $table->string('authorized_signature_url', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_templates');
    }
};
