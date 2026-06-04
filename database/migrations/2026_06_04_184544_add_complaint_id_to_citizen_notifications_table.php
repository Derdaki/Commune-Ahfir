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
        Schema::table('citizen_notifications', function (Blueprint $table) {
            $table->foreignId('complaint_id')->nullable()->after('administrative_request_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citizen_notifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('complaint_id');
        });
    }
};
