<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('billing_period');
            $table->integer('microservice_id');
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('package_id');
            $table->unsignedInteger('payment_method_id');
            $table->unsignedInteger('tenant_id');
            $table->unsignedInteger('created_by_user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
