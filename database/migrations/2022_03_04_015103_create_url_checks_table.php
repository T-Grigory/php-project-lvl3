<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('url_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('url_id');
            $table->foreign('url_id')->references('id')->on('urls');
            $table->string('status_code')->nullable();
            $table->string('h1')->nullable();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('url_checks');
    }
};
