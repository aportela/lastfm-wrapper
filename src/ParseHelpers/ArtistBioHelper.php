<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers;

class ArtistBioHelper
{
    public ?string $summary = null;

    public ?string $content = null;

    private function nl2P(string $text, bool $removeDuplicated = true): string
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

    public function trimSummary(): bool
    {
        if ($this->summary !== null && ($this->summary !== '' && $this->summary !== '0')) {
            // trim "Read more on Last.fm" html link
            $pattern = '/<a href="https:\/\/www\.last\.fm\/.*">Read more on Last.fm<\/a>$/i';
            $replaced = preg_replace($pattern, "", mb_trim($this->summary));
            if (is_string($replaced)) {
                $this->summary = $this->nl2P(mb_trim($replaced), true);
                return (true);
            }
        }

        return (false);
    }
}
