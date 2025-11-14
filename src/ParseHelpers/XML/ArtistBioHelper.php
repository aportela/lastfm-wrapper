<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers\XML;

class ArtistBioHelper extends \aportela\LastFMWrapper\ParseHelpers\ArtistBioHelper
{
    public function __construct(\SimpleXMLElement $element)
    {
        $children = $element->children();
        if ($children != null) {
            $this->summary = empty($children->summary) ? null : (string) $children->summary;
            if (!in_array($this->summary, [null, '', '0'], true)) {
                // trim "Read more on Last.fm" html link
                $pattern = '/<a href="https:\/\/www\.last\.fm\/.*">Read more on Last.fm<\/a>$/i';
                $replaced = preg_replace($pattern, "", mb_trim($this->summary));
                if (is_string($replaced)) {
                    $this->summary = $this->nl2P(mb_trim($replaced), true);
                }
            }

            $this->content = empty($children->content) ? null : (string) $children->content;
        } else {
            throw new \aportela\LastFMWrapper\Exception\InvalidXMLException("artist bio element without children elements");
        }
    }
}
