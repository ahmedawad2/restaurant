<?php

use App\Infra\Classes\Common\Helpers;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    private string $table;

    public function __construct()
    {
        $this->table = 'customers';
    }

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('mobile')->unique();
            $table->string('password');
            $table->string('otp', 5)->nullable();
            $table->boolean('status')->default(false);
            $table->dateTime('created_at');
        });
        Helpers::setDefaultToDateTimeColumn($this->table, 'created_at');
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
