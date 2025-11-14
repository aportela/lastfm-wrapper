<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers;

class ArtistBioHelper
{
    public ?string $summary = null;

    public ?string $content = null;

    public function nl2P(string $text, bool $removeDuplicated = true): string
    {
        $paragraphs = [];
        foreach (explode("\n", $text) as $paragraph) {
            if ($removeDuplicated) {
                if ($paragraph !== '' && $paragraph !== '0') {
                    $paragraphs[] = $paragraph = "<p>" . $paragraph . "</p>";
                }
            } else {
                $paragraphs[] = $paragraph = "<p>" . $paragraph . "</p>";
            }
        }

        return (mb_trim(implode(PHP_EOL, $paragraphs)));
    }
}
