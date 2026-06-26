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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->longText('about');

            $table->string('image')->nullable();

            $table->string('location');

            $table->date('date');

            $table->time('time');

            $table->enum('event_type', [
                'virtual',
                'physical'
            ]);

            $table->string('presented_by');

            $table->string('hosted_by');

            $table->string('host_contact');

            $table->enum('status', [
                'draft',
                'published',
                'cancelled',
                'completed'
            ])->default('draft');

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
