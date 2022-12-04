<?php

namespace Lee\Tests;

use GuzzleHttp\Psr7\Response;
use Lee\Request\Client;
use Lee\Tracker;
use PHPUnit\Framework\TestCase;

class TrackerTest extends TestCase
{
    public function testTrackWithInvalidUrl(): void
    {
        $invalidUrl = 'I am not valid URL';

        $tracker = $this->getMockBuilder(Tracker::class)
                     ->onlyMethods([])
                     ->disableOriginalConstructor()
                     ->getMock();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("The giving URL '$invalidUrl' is invalid.");

        $tracker->track($invalidUrl);
    }

    public function testTrackWithoutRedirects(): void
    {
        $url = 'https://www.php.net/';

        $response = $this->getMockBuilder(Response::class)
            ->onlyMethods(['getStatusCode', 'getHeaders', 'hasHeader'])
            ->disableOriginalConstructor()
            ->getMock();

        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $response->expects($this->once())
            ->method('getHeaders')
            ->willReturn(['content-type' => 'text/html']);

        $response->expects($this->once())
            ->method('hasHeader')
            ->with('Location')
            ->willReturn(false);

        $client = $this->getMockBuilder(Client::class)
            ->onlyMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $client->expects($this->once())
            ->method('get')
            ->with($url)
            ->willReturn($response);

        $tracker = $this->getMockBuilder(Tracker::class)
            ->onlyMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $property = getReflectedProperty(Tracker::class, 'client');
        $property->setValue($tracker, $client);

        $results = $tracker->track($url);
        $final = $results->getFinal();

        $this->assertEquals(200, $final->getCode());
        $this->assertEquals($url, $final->getUrl());
        $this->assertEquals(['content-type' => 'text/html'], $final->getHeaders());
    }

    public function testTrackWithOneRedirect(): void
    {
        $originalUrl = 'https://bit.ly/grpc-intro';
        $finalUrl = 'https://www.slideshare.net/williamyeh/grpc-238408172/williamyeh/grpc-238408172';

        // mock the first response object
        $firstResponse = $this->getMockBuilder(Response::class)
            ->onlyMethods(['getStatusCode', 'getHeaders', 'hasHeader', 'getHeader'])
            ->disableOriginalConstructor()
            ->getMock();

        $firstResponse->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(301);

        $firstResponse->expects($this->once())
            ->method('getHeaders')
            ->willReturn(['location' => $finalUrl]);

        $firstResponse->expects($this->once())
            ->method('hasHeader')
            ->with('Location')
            ->willReturn(true);

        $firstResponse->expects($this->once())
            ->method('getHeader')
            ->with('Location')
            ->willReturn([$finalUrl]);

        // get mocked class of the second response object
        $secondResponse = $this->getMockBuilder(Response::class)
            ->onlyMethods(['getStatusCode', 'getHeaders', 'hasHeader'])
            ->disableOriginalConstructor()
            ->getMock();

        $secondResponse->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $secondResponse->expects($this->once())
            ->method('getHeaders')
            ->willReturn(['content-type' => 'text/html']);

        $secondResponse->expects($this->once())
            ->method('hasHeader')
            ->with('Location')
            ->willReturn(false);

        $client = $this->getMockBuilder(Client::class)
            ->onlyMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $client->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive([$originalUrl], [$finalUrl])
            ->willReturnOnConsecutiveCalls($firstResponse, $secondResponse);

        $tracker = $this->getMockBuilder(Tracker::class)
            ->onlyMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $property = getReflectedProperty(Tracker::class, 'client');
        $property->setValue($tracker, $client);

        $resultsArr = $tracker->track($originalUrl)->asArray();
        $originalUrlResult = $resultsArr[0];
        $finalUrlResult = $resultsArr[1];

        $this->assertEquals(301, $originalUrlResult['code']);
        $this->assertEquals($originalUrl, $originalUrlResult['url']);
        $this->assertEquals(['location' => $finalUrl], $originalUrlResult['headers']);

        $this->assertEquals(200, $finalUrlResult['code']);
        $this->assertEquals($finalUrl, $finalUrlResult['url']);
        $this->assertEquals(['content-type' => 'text/html'], $finalUrlResult['headers']);
    }
}
