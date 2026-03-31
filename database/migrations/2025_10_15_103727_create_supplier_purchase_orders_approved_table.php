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
        Schema::create('supplier_purchase_orders_approved', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_purchase_order_id')->nullable();

            $table->string('webike_logo_url', 255)->nullable();
            $table->string('webike_name', 255)->nullable();
            $table->string('webike_tax_id', 255)->nullable();
            $table->string('webike_street_address', 255)->nullable();
            $table->string('webike_district_sub_district', 255)->nullable();
            $table->string('webike_city_municipality', 255)->nullable();
            $table->string('webike_state_province', 255)->nullable();
            $table->string('webike_postal_code_zip_code', 255)->nullable();
            $table->string('webike_phone_number', 255)->nullable();
            $table->string('webike_country', 255)->nullable();

            $table->string('supplier_name', 255)->nullable();
            $table->string('supplier_address', 255)->nullable();
            $table->string('supplier_address_district', 255)->nullable();
            $table->string('supplier_address_city', 255)->nullable();
            $table->string('supplier_address_state', 255)->nullable();
            $table->string('supplier_address_postal_code', 255)->nullable();
            $table->string('supplier_address_country', 255)->nullable();
            $table->string('supplier_phone', 255)->nullable();
            $table->string('tax_id', 255)->nullable();

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
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_purchase_orders_approved');
    }
};
