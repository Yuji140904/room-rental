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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('month', 7);
            $table->unsignedInteger('rent')->default(0);
            $table->unsignedInteger('electricity')->default(0);
            $table->unsignedInteger('water')->default(0);
            $table->unsignedInteger('total')->default(0);
            $table->date('due_date');
            $table->string('status')->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['room_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
