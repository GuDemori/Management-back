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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('establishment_type_id')
                    ->nullable();
            $table->foreign('establishment_type_id')
                    ->references('id')
                    ->on('establishment_types')
                    ->onDelete('set null');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('client');
            $table->string('document')->unique();
            $table->string('cep')->nullable();
            $table->string('address')->nullable();
            $table->string('number')->nullable();
            $table->string('complement')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('refresh_token')->nullable();
            $table->timestamp('refresh_token_expiry')->nullable();
            $table->timestamps();

            $table->index('name');
            $table->index('email');
            $table->index('city');
            $table->index('establishment_type_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
