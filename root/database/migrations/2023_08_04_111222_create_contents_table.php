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
        Schema::create('contents', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->unsignedBigInteger('course_id')->comment('コースID');
            $table->unsignedBigInteger('admin_id')->comment('管理者ID');
            $table->string('title')->comment('タイトル');
            $table->string('youtube_video_id')->nullable()->comment('YouTubeID');
            $table->text('remarks')->nullable()->comment('備考');
            $table->unsignedSmallInteger('position')->default(0)->comment('ソート番号');
            $table->softDeletesDatetime();
            $table->datetimes();
        });
        DB::statement('ALTER TABLE contents AUTO_INCREMENT = '.config('project.database.auto_increment.contents'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
