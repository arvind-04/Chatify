<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('energy_data', function (Blueprint $table) {
            $table->id();
            $table->float('latitude', 10, 6);
            $table->float('longitude', 10, 6);
            $table->string('location_name');
            $table->float('temperature');
            $table->float('radiation');
            $table->float('energy_potential');
            $table->timestamp('recorded_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('energy_data');
    }
};