<?php

namespace aportela\LastFMWrapper;

class LastFM
{
    const USER_AGENT = "LastFMWrapper - https://github.com/aportela/lastfm-wrapper (766f6964+github@gmail.com)";
    const API_FORMAT_JSON = "json";

    protected $logger;
    protected $http;
    protected $apiFormat;
    protected $apiKey;

    public function __construct(\Psr\Log\LoggerInterface $logger, string $apiFormat, string $apiKey)
    {
        $this->logger = $logger;
        $this->logger->debug("LastFMWrapper\LastFM::__construct");
        $supportedApiFormats = [self::API_FORMAT_JSON];
        if (!in_array($apiFormat, $supportedApiFormats)) {
            $this->logger->critical("LastFMWrapper\LastFM::__construct ERROR: invalid api format");
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormatException("supported formats: " . implode(", ", $supportedApiFormats));
        }
        $this->apiFormat = $apiFormat;
        $loadedExtensions = get_loaded_extensions();
        if (!in_array("libxml", $loadedExtensions)) {
            $this->logger->critical("LastFMWrapper\LastFM::__construct ERROR: libxml extension not found");
            throw new \aportela\LastFMWrapper\Exception\LibXMLMissingException("loaded extensions: " . implode(", ", $loadedExtensions));
        } else if (!in_array("SimpleXML", $loadedExtensions)) {
            $this->logger->critical("LastFMWrapper\LastFM::__construct ERROR: SimpleXML extension not found");
            throw new \aportela\LastFMWrapper\Exception\SimpleXMLMissingException("loaded extensions: " . implode(", ", $loadedExtensions));
        } else {
            $this->logger->debug("LastFMWrapper\LastFM::__construct");
            $this->http = new \aportela\HTTPRequestWrapper\HTTPRequest($this->logger, self::USER_AGENT);
        }
        if (!empty($apiKey)) {
            $this->apiKey = $apiKey;
        } else {
            $this->logger->critical("LastFMWrapper\LastFM::__construct ERROR: API key empty/not found");
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIKeyException("");
        }
    }

    public function __destruct()
    {
        $this->logger->debug("LastFMWrapper\LastFM::__destruct");
    }
}
