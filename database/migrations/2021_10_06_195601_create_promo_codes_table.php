<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoCodesTable extends Migration
{
    private string $table;

    public function __construct()
    {
        $this->table = 'promo_codes';
    }

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->float('reward');
            $table->tinyInteger('type');//1 fixed, 2 percentage.
            $table->dateTime('from')->nullable();
            $table->dateTime('to');
            $table->integer('count')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
