<?php

namespace aportela\LastFMWrapper\ParseHelpers;

class BaseHelper
{
    public function parseDateToYear(?string $date): ?int
    {
        if ($date !== null) {
            switch (mb_strlen(mb_trim($date))) {
                case 10:
                    $dateObj = date_create_from_format('Y-m-d', $date);
                    if ($dateObj !== false) {
                        return (intval(date_format($dateObj, 'Y')));
                    } else {
                        return (null);
                    }
                    // no break
                case 4:
                    return (intval($date));
                default:
                    return (null);
            }
        } else {
            return (null);
        }
    }
}
