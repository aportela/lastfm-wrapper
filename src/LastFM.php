<?php

namespace aportela\LastFMWrapper;

class LastFM
{
    const USER_AGENT = "LastFMWrapper - https://github.com/aportela/lastfm-wrapper (766f6964+github@gmail.com)";

    protected $logger;
    protected $http;

    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->logger->debug("LastFMWrapper::__construct");
        $loadedExtensions = get_loaded_extensions();
        if (!in_array("libxml", $loadedExtensions)) {
            $this->logger->critical("LastFMWrapper::__construct ERROR: libxml extension not found");
            throw new \aportela\LastFMWrapper\Exception\LibXMLMissingException("loaded extensions: " . implode(", ", $loadedExtensions));
        } else if (!in_array("SimpleXML", $loadedExtensions)) {
            $this->logger->critical("LastFMWrapper::__construct ERROR: SimpleXML extension not found");
            throw new \aportela\LastFMWrapper\Exception\SimpleXMLMissingException("loaded extensions: " . implode(", ", $loadedExtensions));
        } else {
            $this->logger->debug("LastFMWrapper::__construct");
            $this->http = new \aportela\HTTPRequestWrapper\HTTPRequest($this->logger, self::USER_AGENT);
        }
    }

    public function __destruct()
    {
        $this->logger->debug("LastFMWrapper::__destruct");
    }
}
