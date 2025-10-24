<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currency_rate_histories', function (Blueprint $table) {
            $table->id();
            $table->string('currency_code', 3)->index();
            $table->decimal('rate_usd', 18, 8);
            $table->string('provider')->nullable();
            $table->timestamp('fetched_at')->index();
            $table->timestamps();
            $table->unique(['currency_code', 'fetched_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_rate_histories');
    }
};
