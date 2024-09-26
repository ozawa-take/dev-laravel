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
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->unsignedBigInteger('admin_id')->comment('管理者ID')->unique();
            $table->datetimes();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });
        DB::statement('ALTER TABLE admin_logs AUTO_INCREMENT = '.config('project.database.auto_increment.admin_logs'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};
