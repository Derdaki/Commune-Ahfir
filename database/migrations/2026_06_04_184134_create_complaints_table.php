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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('citizen_id')->constrained()->restrictOnDelete();
            $table->foreignId('complaint_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('municipal_service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject');
            $table->text('description');
            $table->string('location')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['new', 'processing', 'resolved', 'rejected'])->default('new');
            $table->enum('channel', ['web', 'desk', 'phone', 'email'])->default('web');
            $table->text('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'complaint_category_id', 'municipal_service_id'], 'complaints_filter_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
