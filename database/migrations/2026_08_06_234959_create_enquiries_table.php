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
        Schema::create('enquiries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('organisation');
            $table->string('email');
            $table->string('phone');
            $table->date('event_date');
            $table->string('venue');
            $table->integer('audience_size');
            $table->string('event_type');
            $table->string('service');
            $table->string('budget');
            $table->text('message');
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
