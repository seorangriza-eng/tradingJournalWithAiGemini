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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->string('pair')->nullable();
            $table->enum('position', ['LONG', 'SHORT'])->nullable();
            $table->string('result')->nullable();
            $table->string('media_group_id')->nullable()->index();
            $table->json('chart_images')->nullable(); // Array path gambar
            $table->text('result_image')->nullable();
            $table->text('note_transcript')->nullable();
            $table->boolean('am_method_aligned')->nullable();
            $table->integer('discipline_score')->nullable();
            $table->text('rule_violations')->nullable();
            $table->text('consistency_eval')->nullable();
            $table->text('ai_analysis')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
