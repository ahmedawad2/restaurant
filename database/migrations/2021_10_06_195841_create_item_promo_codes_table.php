<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemPromoCodesTable extends Migration
{
    private string $table;

    public function __construct()
    {
        $this->table = 'item_promo_codes';
    }

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('item_id');
            $table->foreign('item_id')->references('id')
                ->on('items')
                ->onDelete('cascade');

            $table->string('promo_code_id');
            $table->foreign('promo_code_id')->references('id')
                ->on('promo_codes')
                ->onDelete('cascade');

            $table->unsignedInteger('consumed_by')->nullable();
            $table->foreign('consumed_by')->references('id')
                ->on('customers')
                ->onDelete('cascade');

            $table->index('item_id');
            $table->index('promo_code_id');
            $table->index('consumed_by');
        });
    }

    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropIndex(['item_id']);
            $table->dropIndex(['promo_code_id']);
            $table->dropIndex(['consumed_by']);
        });

        Schema::dropIfExists($this->table);
    }
}
