<?php

namespace aportela\LastFMWrapper;

class Entity extends \aportela\LastFMWrapper\LastFM
{

    public $raw;

    public function __construct(\Psr\Log\LoggerInterface $logger, string $apiFormat, string $apiKey)
    {
        parent::__construct($logger, $apiFormat, $apiKey);
        $this->logger->debug("LastFMWrapper\Entity::__construct");
    }

    public function __destruct()
    {
        parent::__destruct();
        $this->logger->debug("LastFMWrapper\Entity::__destruct");
    }
}
