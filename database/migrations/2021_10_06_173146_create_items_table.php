<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    private string $table;

    public function __construct()
    {
        $this->table = 'items';
    }

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedFloat('price');
            $table->unsignedFloat('qty');
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
