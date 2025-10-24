<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subevents', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('event_id')->constrained()->cascadeOnDelete();
            $table->ulid('depends_on')->nullable();
            $table->string('name', 128);
            $table->string('description', 512)->nullable();
            $table->timestampTz('start_date');
            $table->timestampTz('end_date')->nullable();
            $table->char('bg_color', 7)->nullable();
            $table->char('fg_color', 7)->nullable();
            $table->timestamps();
            
            $table->index('start_date');
        });

        Schema::table('subevents', function (Blueprint $table) {
            $table->foreign('depends_on')->references('id')->on('subevents')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subevents');
    }
};
