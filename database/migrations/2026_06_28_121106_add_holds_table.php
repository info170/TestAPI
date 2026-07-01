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
        Schema::create('holds', function (Blueprint $table) {
            $table->id('hold_id');
            $table->unsignedBigInteger('slot_id');
            $table->string('idempotency_key');
            $table->enum('status', ['held', 'confirmed', 'cancelled']);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->foreign('slot_id', 'fk_slot_id')
                ->references('slot_id')
                ->on('slots')
                ->restrictOnDelete()
                ->restrictOnUpdate();

            $table->index(['slot_id', 'status'], 'idx_slot_status');
            $table->index('idempotency_key', 'idx_idempotency');
            $table->unique(['slot_id', 'idempotency_key'], 'idx_slot_idempotency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holds');
    }
};
