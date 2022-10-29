<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');

            $table->integer('views')
                ->default(0);

            $table->foreignIdFor(User::class)
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignIdFor(Category::class)
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->integer('status')
                ->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (app()->isLocal()) {
            Schema::dropIfExists('articles');
        }
    }
};
