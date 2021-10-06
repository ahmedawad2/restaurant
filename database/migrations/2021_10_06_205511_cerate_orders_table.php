<?php

use App\Infra\Classes\Common\Helpers;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CerateOrdersTable extends Migration
{
    private string $table;

    public function __construct()
    {
        $this->table = 'orders';
    }

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->string('id', 25)->unique();

            $table->unsignedInteger('reservation_id');
            $table->foreign('reservation_id')->references('id')
                ->on('reservations')
                ->onDelete('cascade');

            $table->decimal('total');
            $table->decimal('sub_total');
            $table->dateTime('created_at');

            $table->index('reservation_id');
        });
        Helpers::setDefaultToDateTimeColumn($this->table, 'created_at');
    }

    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropIndex(['reservation_id']);
        });

        Schema::dropIfExists($this->table);
    }
}
