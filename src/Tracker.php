<?php

namespace Lee;

use GuzzleHttp\Client;
use Lee\Result\Result;
use Lee\Result\Set;

class Tracker
{
    /** @var string */
    private static $url = '';

    public function __construct(string $url)
    {
        self::$url = $url;
    }

    /**
     * track url with static method call.
     */
    public static function trackFromUrl(string $url): Set
    {
        self::validateUrl($url);
        $client = new Client();
        $results = new Set();
        $response = $client->request('GET', $url, ['allow_redirects' => false]);
        $results->add(new Result($response->getStatusCode(), $url, $response->getHeaders()));
        while ($response->hasHeader('Location') === true) {
            $redirectUrl = $response->getHeader('Location')[0];
            $response = $client->request('GET', $redirectUrl, ['allow_redirects' => false]);
            $code = $response->getStatusCode();
            $headers = $response->getHeaders();
            $results->add(new Result($code, $redirectUrl, $headers));
        }

        return $results;
    }

    /**
     * track method.
     */
    public function track(): Set
    {
        return self::trackFromUrl(self::$url);
    }

    /**
     * url getter method.
     */
    public function getUrl(): string
    {
        return self::$url;
    }

    /**
     * validate private static url with filter_var function.
     */
    private static function validateUrl(string $url): bool
    {
        $validatedResult = filter_var($url, FILTER_VALIDATE_URL);
        if ($validatedResult === false) {
            $exceptionMessage = sprintf('The %s is invalid.', $url);

            throw new \InvalidArgumentException($exceptionMessage);
        }

        return true;
    }
}
