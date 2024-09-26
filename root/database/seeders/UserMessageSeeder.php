<?php

namespace Database\Seeders;

use App\Models\UserMessage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class UserMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(app()->isLocal()) {
            UserMessage::factory()
                ->count(30)
                ->sequence(function($sequence) {
                    return [
                        'admin_id' => config('project.database.auto_increment.admins'),
                        'user_id' => config('project.database.auto_increment.users'),
                        'title' => sprintf('受講者からのメッセージ%d', $sequence->index + 1),
                        'text' => sprintf('受講者からのメッセージ%d', $sequence->index + 1),
                        'action' => random_int(0,1),
                        'is_hidden' => 0,
                        'is_replied' => 0,
                        'reply_message_id' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->create();
        }
    }
}
