<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * up() runs when we run "php artisan migrate".
     * It CREATES the table.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();                   // auto-increasing ID: 1, 2, 3...
            $table->string('name');         // full name (text up to 255 characters)
            $table->string('email')->unique(); // email; no two students may share one
            $table->string('course',100);       // course name
            $table->timestamps();           // adds created_at and updated_at
        });
    }

    /**
     * down() runs when we undo ("rollback") the migration.
     * It REMOVES the table.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};