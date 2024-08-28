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
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('short_id')->unique();
            $table->string('long_url');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            
            $table->tinyInteger('is_password_protected')->default(0);
            $table->string('password')->nullable();
            
            $table->dateTime('expires_at')->nullable();
            $table->tinyInteger('delete_after_expired')->default(0);
            
            $table->tinyInteger('has_utm_params')->default(0);
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();

            $table->string('choice_page_title')->nullable();
            $table->string('choice_page_logo')->nullable();
            $table->string('choice_page_call_to_action')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
