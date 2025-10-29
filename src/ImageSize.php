<?php

namespace aportela\LastFMWrapper;

enum ImageSize: string
{
    case NONE = "";
    case SMALL = "small";
    case MEDIUM = "medium";
    case LARGE = "large";
    case EXTRA_LARGE = "extralarge";
    case MEGA = "mega";

    public function toString(): string
    {
        return match ($this) {
            self::NONE => "",
            self::SMALL => "small",
            self::MEDIUM => "medium",
            self::LARGE => "large",
            self::EXTRA_LARGE => "extralarge",
            self::MEGA => "mega",
        };
    }

    public static function fromString(string $value): ImageSize
    {
        return match ($value) {
            "" => self::NONE,
            "small" => self::SMALL,
            "medium" => self::MEDIUM,
            "large" => self::LARGE,
            "extralarge" => self::EXTRA_LARGE,
            "mega" => self::MEGA,
            default => self::NONE,
        };
    }
}
