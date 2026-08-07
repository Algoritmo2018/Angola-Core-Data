<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('municipalities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
              $table->uuid('province_id')->nullable();
            $table->foreign('province_id')->references('id')->on('provinces');
             $table->timestamps();
            $table->softDeletes(); //adiciona a coluna deleted_at na tabela
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipalities');
    }
};
