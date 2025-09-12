<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('filename');
            $table->string('original_name')->nullable();
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('checksum', 128)->nullable();
            $table->string('local_path')->nullable();
            $table->string('storage_path');
            $table->string('qr_token', 64)->unique();
            $table->string('visibility', 20)->default('public'); // public|private|unlisted
            $table->string('status', 20)->default('pending'); // pending|ready|failed
            $table->timestamp('captured_at')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->string('event_id')->nullable();
            $table->timestamps();

            $table->index('checksum');
            $table->index('captured_at');
            $table->index('event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};

