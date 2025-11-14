<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\JSON;

class ArtistBioHelper extends \aportela\LastFMWrapper\ParseHelpers\ArtistBioHelper
{
    public function __construct(object $object)
    {
        $this->summary = property_exists($object, "summary") && is_string($object->summary) ? $object->summary : null;
        if (!in_array($this->summary, [null, '', '0'], true)) {
            // trim "Read more on Last.fm" html link
            $pattern = '/<a href="https:\/\/www\.last\.fm\/.*">Read more on Last.fm<\/a>$/i';
            $replaced = preg_replace($pattern, "", mb_trim($this->summary));
            if (is_string($replaced)) {
                $this->summary = $this->nl2P(mb_trim($replaced), true);
            }
        }

        $this->content = property_exists($object, "content") && is_string($object->content) ? $object->content : null;
    }
}
