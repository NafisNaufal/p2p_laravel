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
        Schema::create('file_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('sender_session_id');
            $table->string('receiver_session_id');
            $table->string('filename');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->bigInteger('file_size');
            $table->string('file_hash')->nullable();
            $table->ipAddress('sender_ip');
            $table->ipAddress('receiver_ip');
            $table->enum('transfer_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamps();

            $table->index(['sender_session_id', 'receiver_session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_transfers');
    }
};
