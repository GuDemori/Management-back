<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('cep', 9)->nullable();
            $table->string('address')->nullable();
            $table->string('number', 10)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();

            $table->index('city');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
