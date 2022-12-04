<?php

namespace Lee\Tests\Request;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Psr7\Response;
use Lee\Request\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testConstructorWithInvalidRequestOptions(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Option 'foo' does not supported as a Guzzle request option.");

        new Client([
            'timeout' => 3,
            'foo' => 'bar',
        ]);
    }

    public function testGetWithValidUrl(): void
    {
        $url = 'https://bit.ly/grpc-intro';

        $guzzle = $this->getMockBuilder(GuzzleHttpClient::class)
            ->onlyMethods(['get'])
            ->getMock();

        $guzzle->expects($this->once())
            ->method('get')
            ->with($url)
            ->willReturn(new Response(301));

        $client = $this->getMockBuilder(Client::class)
            ->onlyMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $property = getReflectedProperty(Client::class, 'client');
        $property->setValue($client, $guzzle);

        $this->assertInstanceOf(Response::class, $client->get($url));
    }

    public function testGetWithInvalidUrl(): void
    {
        $guzzle = $this->getMockBuilder(GuzzleHttpClient::class)
            ->onlyMethods([])
            ->getMock();

        $client = $this->getMockBuilder(Client::class)
            ->onlyMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $property = getReflectedProperty(Client::class, 'client');
        $property->setValue($client, $guzzle);

        $this->expectException(\GuzzleHttp\Exception\GuzzleException::class);
        $client->get('I am not valid URL');
    }
}
