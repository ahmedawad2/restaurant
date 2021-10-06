<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsSlotsTable extends Migration
{
    private string $table;

    public function __construct()
    {
        $this->table = 'items_slots';
    }

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('item_id');
            $table->foreign('item_id')->references('id')
                ->on('items')
                ->onDelete('cascade');

            $table->time('from');
            $table->time('to');

            $table->index('item_id');
        });
    }

    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropIndex(['item_id']);
        });

        Schema::dropIfExists($this->table);
    }
}
