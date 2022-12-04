<?php

// todo add @internal

namespace Lee\Request;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;

final class Client
{
    private GuzzleClient $client;

    public function __construct(array $options)
    {
        if ($options) {
            $this->validateOptions($options);
        }

        $options['allow_redirects'] = false;

        $this->client = new GuzzleClient($options);
    }

    public function get(string $url): Response
    {
        return $this->client->get($url);
    }

    /**
     * Guzzle client implementation do not raise any error/exception upon invalid requests
     * options, this method validates request options to ensure they can be safely passed
     * into Guzzle client and throws an exception if any of the options is not supported.
     *
     * @throws \LogicException
     */
    private function validateOptions(array $options): void
    {
        foreach (array_keys($options) as $name) {
            if (!defined(sprintf('%s::%s', RequestOptions::class, strtoupper($name)))) {
                throw new \LogicException("Option '$name' does not supported as a Guzzle request option.");
            }
        }
    }
}