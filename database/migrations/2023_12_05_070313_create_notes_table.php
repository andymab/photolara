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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('parent_id')->default(0)->index();
            $table->string("path_logo");
            $table->string('name',80)->unique();
            $table->string('slug',90)->unique(); //eng -> name
            $table->string('descr',255);
            $table->longText('post_md'); //post .md format
            $table->longText('post_js'); //js model when ID -> slug
            $table-json('path_images'); //array path to images
            $table->timestamps();
            //также могуг быть прикреплены картинки note_images
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
