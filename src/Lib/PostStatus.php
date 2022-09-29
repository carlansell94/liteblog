<?php

namespace carlansell94\Liteblog\Lib;

enum PostStatus: int
{
    case DRAFT = 0;
    case PUBLISHED = 1;

    public function toString(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published'
        };
    }

    public function toAction(): string
    {
        return match ($this) {
            self::DRAFT => 'Publish',
            self::PUBLISHED => 'Draft'
        };
    }
}
