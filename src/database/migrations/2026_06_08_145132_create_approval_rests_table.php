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
        Schema::create('approval_rests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_request_id')->constrained('approval_requests')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('rest_id')->nullable()->constrained('rests')->cascadeOnUpdate()->cascadeOnDelete();
            $table->time('request_rest_start_at');
            $table->time('request_rest_finish_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_rests');
    }
};
