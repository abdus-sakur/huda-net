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
        Schema::create('payment_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('month');
            $table->string('year');
            $table->integer('total_customers')->default(0);
            $table->integer('paid_customers')->default(0);
            $table->integer('unpaid_customers')->default(0);
            $table->decimal('total_paid', 12, 2)->default(0.00);
            $table->decimal('total_unpaid', 12, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_summaries');
    }
};
