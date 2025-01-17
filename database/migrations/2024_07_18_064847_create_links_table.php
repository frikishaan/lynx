<?php

use App\Models\Domain;
use App\Models\Team;
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
            $table->string('short_id');
            $table->foreignIdFor(Domain::class)->nullable()->constrained()->nullOnDelete();
            $table->string('long_url');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            
            // Password protection
            $table->string('password')->nullable();
            
            // Expiry
            $table->dateTime('expires_at')->nullable();
            $table->tinyInteger('delete_after_expired')->default(0);
            
            // UTM Parameters
            $table->tinyInteger('has_utm_params')->default(0);
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();

            // choice page
            $table->text('choice_page_image')->nullable();
            $table->string('choice_page_title')->nullable();
            $table->text('choice_page_description')->nullable();
            $table->tinyInteger('enable_dark_mode')->default(0);

            $table->foreignIdFor(Team::class)->constrained()->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['short_id', 'domain_id']);
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
