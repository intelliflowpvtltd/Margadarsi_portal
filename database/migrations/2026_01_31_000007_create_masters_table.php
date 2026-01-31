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
        Schema::create('masters', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50); // project_feature, document_type, payment_method, currency, language, etc.
            $table->string('name', 100);
            $table->string('slug', 100);
            $table->string('value', 255)->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('masters')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('type');
            $table->index('is_active');
            $table->index(['type', 'is_active']);
            $table->unique(['type', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masters');
    }
};
