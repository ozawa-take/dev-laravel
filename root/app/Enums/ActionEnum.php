<?php

namespace App\Enums;

enum ActionEnum: int
{
    case DRAFT = 0;
    case SEND  = 1;
    case NO_REPLY = 2;

    public function label(): string
    {
        return match ($this) {
            self::DRAFT    => '下書き',
            self::SEND     => '送信',
            self::NO_REPLY => '未返信',
        };
    }
}
