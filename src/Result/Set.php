<?php

namespace Lee\Result;

class Set implements \Countable
{
    /** @var array<Result> */
    private array $results = [];

    public function add(Result $result): self
    {
        $this->results[] = $result;

        return $this;
    }

    /**
     * @throws \RuntimeException
     *
     * @return array<int, array<string, int|string|array<string, string|string[]>>>
     */
    public function asArray(): array
    {
        $doceded = @json_decode($this->asJson(), true);
        if (!is_array($doceded)) {
            throw new \RuntimeException('Could not decode JSON results string into an associative array: ' . json_last_error_msg());
        }

        return $doceded;
    }

    /**
     * @throws \RuntimeException
     */
    public function asJson(): string
    {
        $json = json_encode($this->results);
        if ($json === false) {
            throw new \RuntimeException('Could not encode results into JSON format: ' . json_last_error_msg());
        }

        return $json;
    }

    /**
     * Gets the last result in the results set, which holds the final destination URL of the redirect loop.
     */
    public function getFinal(): Result
    {
        return $this->results[$this->count() - 1];
    }

    public function count(): int
    {
        return count($this->results);
    }
}
