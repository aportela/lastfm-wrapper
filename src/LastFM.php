<?php

declare(strict_types=1);

namespace aportela\LastFMWrapper;

abstract class LastFM
{
    public const USER_AGENT = "LastFMWrapper - https://github.com/aportela/lastfm-wrapper (766f6964+github@gmail.com)";

    protected \aportela\HTTPRequestWrapper\HTTPRequest $http;

    protected \aportela\LastFMWrapper\APIFormat $apiFormat;

    protected string $apiKey;

    public function __construct(protected \Psr\Log\LoggerInterface $logger, \aportela\LastFMWrapper\APIFormat $apiFormat, string $apiKey)
    {
        $this->http = new \aportela\HTTPRequestWrapper\HTTPRequest($this->logger, self::USER_AGENT);
        $supportedApiFormats = [\aportela\LastFMWrapper\APIFormat::XML, \aportela\LastFMWrapper\APIFormat::JSON];
        if (!in_array($apiFormat, $supportedApiFormats)) {
            $this->logger->critical(\aportela\LastFMWrapper\LastFM::class . '::__construct - ERROR: invalid api format', [$apiFormat, [\aportela\LastFMWrapper\APIFormat::XML->value, \aportela\LastFMWrapper\APIFormat::JSON->value]]);
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIFormat("supported formats: " . implode(", ", [\aportela\LastFMWrapper\APIFormat::XML->value, \aportela\LastFMWrapper\APIFormat::JSON->value]));
        }

        $this->apiFormat = $apiFormat;
        $loadedExtensions = get_loaded_extensions();
        foreach (["dom", "libxml", "SimpleXML"] as $requiredExtension) {
            if (!in_array($requiredExtension, $loadedExtensions)) {
                $this->logger->critical(sprintf('\aportela\MusicBrainzWrapper\LastFM::__construct - ERROR: %s php extension not found', $requiredExtension), $loadedExtensions);
                throw new \aportela\LastFMWrapper\Exception\PHPExtensionMissingException(sprintf('Missing required php extension: %s, loaded extensions: ', $requiredExtension) . implode(", ", $loadedExtensions));
            }
        }

        // avoids simplexml_load_string warnings
        // https://stackoverflow.com/a/40585185
        libxml_use_internal_errors(true);
        if ($apiKey === '' || $apiKey === '0') {
            $this->logger->critical("\aportela\MusicBrainzWrapper\LastFM::__construct - ERROR: empty API KEY");
            throw new \aportela\LastFMWrapper\Exception\InvalidAPIKeyException("empty key");
        }

        $this->apiKey = $apiKey;
    }
}
