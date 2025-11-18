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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('vendor_product_variant_stocks');
        Schema::dropIfExists('vendor_product_variants');
        Schema::dropIfExists('vendor_products');
        Schema::dropIfExists('product_variant_stocks');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::enableForeignKeyConstraints();

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->boolean('is_active')->comment('activation for the bank of products to show for the vendors');
            $table->enum('configuration_type', ['simple', 'with_variants'])->default('simple');
            // Approval workflow
            $table->enum('status', ['pending', 'approved', 'requested', 'rejected'])->default('pending');
            $table->text('status_message')->comment('message of the status will be like rejected give a message')->nullable();
            // If vendor created it
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->cascadeOnDelete();

            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('sub_category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('variant_configuration_id')->constrained('variants_configurations')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('vendor_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('tax_id')->constrained('taxes')->cascadeOnDelete();
            $table->string('sku');
            $table->integer('points');
            $table->integer('max_per_order');
            $table->boolean('offer_date_view')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('vendor_product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_product_id')->constrained('vendor_products')->cascadeOnDelete();
            $table->foreignId('variant_configuration_id')->constrained('variants_configurations')->cascadeOnDelete();
            $table->string('sku');
            $table->decimal('price', 20, 2);
            $table->boolean('has_offer')->default(false);
            $table->decimal('price_before_discount', 20, 2);
            $table->date('offer_end_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('vendor_product_variant_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_product_variant_id')->constrained('vendor_product_variants')->cascadeOnDelete();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->integer('quantity');
            $table->softDeletes();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('vendor_product_variant_stocks');
        Schema::dropIfExists('vendor_product_variants');
        Schema::dropIfExists('vendor_products');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
    }
};
