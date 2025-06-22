<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->string('client_name');
            $table->string('client_address_street')->nullable();
            $table->string('client_address_number')->nullable();
            $table->string('client_address_district')->nullable();
            $table->string('client_address_city')->nullable();
            $table->string('client_address_state')->nullable();
            $table->string('client_address_zipcode')->nullable();
            $table->decimal('total_value', 10, 2)->default(0);
            $table->enum('status', ['Em espera', 'Preparando', 'À caminho', 'Entregue', 'Pago', 'Cancelado'])
                    ->default('Em espera');
            $table->timestamps();

            $table->index('client_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}