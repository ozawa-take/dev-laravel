<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('groups_courses', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->unsignedBigInteger('group_id')->comment('グループID');
            $table->unsignedBigInteger('course_id')->comment('コースID');
            $table->softDeletesDatetime();
            $table->datetimes();

            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');

            $table->unique(['group_id', 'course_id']);
        });
        DB::statement('ALTER TABLE groups_courses AUTO_INCREMENT = '.config('project.database.auto_increment.groups_courses'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups_courses');
    }
};
