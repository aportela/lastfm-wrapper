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
        $this->http = new \aportela\HTTPRequestWrapper\HTTPRequest($this->logger, self::USER_AGENT);
        $supportedApiFormats = [\aportela\LastFMWrapper\APIFormat::XML, \aportela\LastFMWrapper\APIFormat::JSON];
        if (!in_array($apiFormat, $supportedApiFormats)) {
            $this->logger->critical("LastFMWrapper::__construct ERROR: invalid api format");
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormat("supported formats: " . implode(", ", [\aportela\LastFMWrapper\APIFormat::XML->value, \aportela\LastFMWrapper\APIFormat::JSON->value]));
        }
        $this->apiFormat = $apiFormat;
        if ($apiFormat == \aportela\LastFMWrapper\APIFormat::XML) {
            $loadedExtensions = get_loaded_extensions();
            if (!in_array("libxml", $loadedExtensions)) {
                $this->logger->critical("LastFMWrapper\LastFM::__construct ERROR: libxml extension not found");
                throw new \aportela\LastFMWrapper\Exception\LibXMLMissingException("loaded extensions: " . implode(", ", $loadedExtensions));
            } elseif (!in_array("SimpleXML", $loadedExtensions)) {
                $this->logger->critical("LastFMWrapper\LastFM::__construct ERROR: SimpleXML extension not found");
                throw new \aportela\LastFMWrapper\Exception\SimpleXMLMissingException("loaded extensions: " . implode(", ", $loadedExtensions));
            }
            // avoids simplexml_load_string warnings
            // https://stackoverflow.com/a/40585185
            libxml_use_internal_errors(true);
        }
        if (empty($apiKey)) {
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIKeyException("empty key");
        }
        $this->apiKey = $apiKey;
    }

    public function __destruct()
    {
        $this->logger->debug("LastFMWrapper\LastFM::__destruct");
    }
}
