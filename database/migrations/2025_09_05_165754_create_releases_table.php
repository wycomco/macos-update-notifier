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
        Schema::create('releases', function (Blueprint $table) {
            $table->id();
            $table->string('major_version'); // e.g., "macOS 14", "macOS 15"
            $table->string('version'); // e.g., "14.6.1", "15.0.1"
            $table->timestamp('release_date');
            $table->json('raw_json'); // Full entry from SOFA feed for debugging/reference
            $table->timestamps();
            
            // Add unique constraint for major_version + version
            $table->unique(['major_version', 'version']);
            // Index for faster lookups
            $table->index(['major_version', 'release_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};
