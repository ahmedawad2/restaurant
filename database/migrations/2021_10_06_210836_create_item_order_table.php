<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemOrderTable extends Migration
{
    private string $table;

    public function __construct()
    {
        $this->table = 'item_order';
    }

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('item_id');
            $table->foreign('item_id')->references('id')
                ->on('items')
                ->onDelete('cascade');

            $table->string('order_id');
            $table->foreign('order_id')->references('id')
                ->on('orders')
                ->onDelete('cascade');

            $table->index('item_id');
            $table->index('order_id');
        });
    }

    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropIndex(['item_id']);
            $table->dropIndex(['order_id']);
        });

        Schema::dropIfExists($this->table);
    }
}
