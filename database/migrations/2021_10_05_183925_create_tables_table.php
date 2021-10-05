<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablesTable extends Migration
{
    private string $table;

    public function __construct()
    {
        $this->table = 'tables';
    }

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('capacity');
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
