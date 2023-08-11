<?php

namespace aportela\LastFMWrapper;

class LastFM
{
    public const USER_AGENT = "LastFMWrapper - https://github.com/aportela/lastfm-wrapper (766f6964+github@gmail.com)";

    protected \Psr\Log\LoggerInterface $logger;
    protected \aportela\HTTPRequestWrapper\HTTPRequest $http;
    protected \aportela\LastFMWrapper\APIFormat $apiFormat;
    protected string $apiKey;

    public function __construct(\Psr\Log\LoggerInterface $logger, \aportela\LastFMWrapper\APIFormat $apiFormat, string $apiKey)
    {
        $this->logger = $logger;
        $this->logger->debug("LastFMWrapper\LastFM::__construct");
        $this->apiFormat = $apiFormat;
        $loadedExtensions = get_loaded_extensions();
        if (!in_array("libxml", $loadedExtensions)) {
            $this->logger->critical("LastFMWrapper\LastFM::__construct ERROR: libxml extension not found");
            throw new \aportela\LastFMWrapper\Exception\LibXMLMissingException("loaded extensions: " . implode(", ", $loadedExtensions));
        } elseif (!in_array("SimpleXML", $loadedExtensions)) {
            $this->logger->critical("LastFMWrapper\LastFM::__construct ERROR: SimpleXML extension not found");
            throw new \aportela\LastFMWrapper\Exception\SimpleXMLMissingException("loaded extensions: " . implode(", ", $loadedExtensions));
        } else {
            $this->logger->debug("LastFMWrapper\LastFM::__construct");
            $this->http = new \aportela\HTTPRequestWrapper\HTTPRequest($this->logger, self::USER_AGENT);
        }
        $this->apiKey = $apiKey;
    }

    public function __destruct()
    {
        $this->logger->debug("LastFMWrapper\LastFM::__destruct");
    }
}
