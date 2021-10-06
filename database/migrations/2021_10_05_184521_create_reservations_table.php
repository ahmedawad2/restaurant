<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Infra\Classes\Common\Helpers;

class CreateReservationsTable extends Migration
{
    private string $table;

    public function __construct()
    {
        $this->table = 'reservations';
    }

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('customer_id');
            $table->foreign('customer_id')->references('id')
                ->on('customers')
                ->onDelete('cascade');

            $table->unsignedInteger('table_id');
            $table->foreign('table_id')->references('id')
                ->on('tables')
                ->onDelete('cascade');

            $table->dateTime('from');
            $table->dateTime('to');
            $table->unsignedTinyInteger('status');
            $table->dateTime('created_at');

            $table->index('table_id');
            $table->index('customer_id');
        });
        Helpers::setDefaultToDateTimeColumn($this->table, 'created_at');
    }

    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropIndex(['table_id']);
            $table->dropIndex(['customer_id']);
        });

        Schema::dropIfExists($this->table);
    }
}
