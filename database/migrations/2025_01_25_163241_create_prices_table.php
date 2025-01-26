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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advert_id')->constrained('adverts')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->float('value')->unsigned()->nullable();
            $table->string('currency')->nullable();
            $table->boolean('negotiable')->default(false);
            $table->boolean('trade')->default(false);
            $table->boolean('budget')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
