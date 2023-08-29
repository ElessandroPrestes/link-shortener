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
        Schema::create('access_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('short_link_id');
            $table->string('ip_address');
            $table->string('user_agent');
            $table->timestamps();

            $table->foreign('short_link_id')->references('id')->on('short_links')->onDelete('cascade');
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acces_logs');
    }
};
