<?php

namespace aportela\LastFMWrapper;

class Entity extends \aportela\LastFMWrapper\LastFM
{
    public ?string $raw;

    public function __construct(\Psr\Log\LoggerInterface $logger, \aportela\LastFMWrapper\APIFormat $apiFormat, string $apiKey)
    {
        parent::__construct($logger, $apiFormat, $apiKey);
        $this->logger->debug("LastFMWrapper\Entity::__construct");
        // avoids simplexml_load_string warnings
        // https://stackoverflow.com/a/40585185
        libxml_use_internal_errors(true);
    }

    public function __destruct()
    {
        parent::__destruct();
        $this->logger->debug("LastFMWrapper\Entity::__destruct");
    }

    protected function parseHTTPResponseToObject(string $httpResponse): mixed
    {
        $data = null;
        switch ($this->apiFormat) {
            case \aportela\LastFMWrapper\APIFormat::JSON:
                $data = json_decode($httpResponse);
                if (json_last_error() != JSON_ERROR_NONE) {
                    throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponseFormatException("invalid json");
                }
                break;
            case \aportela\LastFMWrapper\APIFormat::XML:
                $data = simplexml_load_string($httpResponse);
                if ($data === false) {
                    throw new \aportela\LastFMWrapper\Exception\InvalidAPIResponseFormatException("invalid xml");
                }
                break;
        }
        return ($data);
    }
}
