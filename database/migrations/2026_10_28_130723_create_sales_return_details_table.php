
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_return_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sales_return_id')
                ->constrained('sales_returns')
                ->cascadeOnDelete();

            $table->foreignId('transaction_detail_id')
                ->constrained('transaction_details')
                ->restrictOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity');

            $table->enum('kondisi', ['layak', 'rusak']);

            $table->decimal('harga', 15, 2);

            $table->decimal('subtotal', 15, 2);

            $table->string('alasan', 255)->nullable();

            $table->timestamps();

            $table->index(['transaction_detail_id', 'kondisi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_return_details');
    }
};
