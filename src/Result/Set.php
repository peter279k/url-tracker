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
     */
    public function asArray(): array
    {
        $doceded = @json_decode($this->asJson(), true);
        if (!$doceded) {
            throw new \RuntimeException(sprintf(
                'Could not decode JSON results string into an associative array: %s',
                json_last_error_msg()
            ));
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
            throw new \RuntimeException(sprintf(
                'Could not encode results into JSON format: %s',
                json_last_error_msg()
            ));
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
