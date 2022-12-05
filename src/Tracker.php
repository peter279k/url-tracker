<?php

namespace Lee;

use Lee\Request\Client;
use Lee\Result\Result;
use Lee\Result\Set;

class Tracker
{
    private Client $client;

    /**
     * @param array<string, mixed> $requestOptions
     */
    public function __construct(array $requestOptions = [])
    {
        $this->client = new Client($requestOptions);
    }

    /**
     * track method.
     */
    public function track(string $url): Set
    {
        $this->validateUrl($url);

        $results = new Set();
        $response = $this->client->get($url);
        $results->add(new Result($response->getStatusCode(), $url, $response->getHeaders()));
        while ($response->hasHeader('Location') === true) {
            $redirectUrl = $response->getHeader('Location')[0];
            $response = $this->client->get($redirectUrl);
            $results->add(new Result($response->getStatusCode(), $redirectUrl, $response->getHeaders()));
        }

        return $results;
    }

    /**
     * @param array<string, mixed> $requestOptions
     */
    public static function trackFromUrl(string $url, array $requestOptions = []): Set
    {
        $tracker = new static($requestOptions);

        return $tracker->track($url);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function validateUrl(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException("The giving URL '$url' is invalid.");
        }

        return true;
    }
}
