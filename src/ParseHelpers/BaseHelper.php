<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper\ParseHelpers;

class BaseHelper
{
    public ?string $mbId = null;

    protected function getObjectStringProperty(object $object, string $property): string|null
    {
        return (property_exists($object, $property) && is_string($object->{$property}) && ($object->{$property} !== '' && $object->{$property} !== '0') ? $object->{$property} : null);
    }

    protected function getObjectIntegerProperty(object $object, string $property): int|null
    {
        return (property_exists($object, $property) && is_numeric($object->{$property}) ? intval($object->{$property}) : null);
    }
}
