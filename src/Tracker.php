<?php

namespace Lee;

use GuzzleHttp\Client;
use InvalidArgumentException;

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
     *
     * @return array<string>
     */
    public static function trackFromUrl(string $url): array
    {
        self::validateUrl($url);

        $trackedUrl = [$url];
        $client = new Client();
        $response = $client->request('GET', $url, [
            'allow_redirects' => false,
        ]);
        while ($response->hasHeader('Location') === true) {
            $trackedUrl[] = $response->getHeader('Location')[0];
            $url = $trackedUrl[count($trackedUrl) - 1];
            $response = $client->request('GET', $url, [
                'allow_redirects' => false,
            ]);
        }

        return $trackedUrl;
    }

    /**
     * track method.
     *
     * @return array<string>
     */
    public function track(): array
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

            throw new InvalidArgumentException($exceptionMessage);
        }

        return true;
    }
}
