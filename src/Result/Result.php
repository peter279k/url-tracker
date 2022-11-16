<?php

namespace Lee\Result;

class Result implements \JsonSerializable
{
    private int $code;
    private string $url;
    /** @var array<string, string|string[]> */
    private array $headers;

    /**
     * @param array<string, string|string[]> $headers
     */
    public function __construct(int $code, string $url, array $headers)
    {
        $this->code = $code;
        $this->url = $url;
        $this->headers = $headers;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return array<string, string|string[]>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array<string, int|string|array<string, string|string[]>>
     */
    public function jsonSerialize(): array
    {
        return [
            'code' => $this->code,
            'url' => $this->url,
            'headers' => $this->headers,
        ];
    }
}
