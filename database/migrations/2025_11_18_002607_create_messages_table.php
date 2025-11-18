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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('sender_session_id');
            $table->string('receiver_session_id');
            $table->text('message');
            $table->string('message_type')->default('text'); // text, image, file
            $table->ipAddress('sender_ip');
            $table->ipAddress('receiver_ip');
            $table->boolean('is_delivered')->default(false);
            $table->timestamps();

            $table->index(['sender_session_id', 'receiver_session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
