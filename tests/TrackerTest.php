<?php

namespace Lee\Tests;

use Lee\Result\Result;
use Lee\Result\Set;
use Lee\Tracker;
use PHPUnit\Framework\TestCase;

class TrackerTest extends TestCase
{
    public function testTrackOnSpecificShortenBitlyUrl(): void
    {
        $url = 'https://bit.ly/grpc-intro';
        $mock = $this->getMockBuilder(Tracker::class)
                     ->setConstructorArgs([$url])
                     ->getMock();

        $finalResult = new Result(200, 'https://www.slideshare.net/williamyeh/grpc-238408172/williamyeh/grpc-238408172', []);

        $expected = (new Set())
            ->add(new Result(301, $url, ['location' => $finalResult->getUrl()]))
            ->add($finalResult);

        $mock->expects($this->once())
             ->method('track')
             ->willReturn($expected);

        $actual = $mock->track();

        $this->assertEquals($expected, $actual);

        // SetTest
        $this->assertEquals([
            [
                'code' => 301,
                'url' => 'https://bit.ly/grpc-intro',
                'headers' => ['location' => 'https://www.slideshare.net/williamyeh/grpc-238408172/williamyeh/grpc-238408172'],
            ],
            [
                'code' => 200,
                'url' => 'https://www.slideshare.net/williamyeh/grpc-238408172/williamyeh/grpc-238408172',
                'headers' => [],
            ],
        ], $actual->asArray());
        $resultsJson = '[{"code":301,"url":"https:\/\/bit.ly\/grpc-intro","headers":{"location":"https:\/\/'
            . 'www.slideshare.net\/williamyeh\/grpc-238408172\/williamyeh\/grpc-238408172"}},{"code":200,"url":"ht'
            . 'tps:\/\/www.slideshare.net\/williamyeh\/grpc-238408172\/williamyeh\/grpc-238408172","headers":[]}]';
        $this->assertEquals($resultsJson, $actual->asJson());
        $this->assertEquals($finalResult, $actual->getFinal());
        $this->assertEquals(2, $actual->count());

        // ResultTest
        $this->assertEquals(200, $finalResult->getCode());
        $this->assertEquals('https://www.slideshare.net/williamyeh/grpc-238408172/williamyeh/grpc-238408172', $finalResult->getUrl());
        $this->assertEquals([], $finalResult->getHeaders());
    }

    public function testGetUrl(): void
    {
        $url = 'https://bit.ly/grpc-intro';
        $tracker = new Tracker($url);

        $this->assertSame($url, $tracker->getUrl());
    }

    public function testGetUrlShouldThrowInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $url = 'I am not valid URL';
        $tracker = new Tracker($url);
        $tracker->track();
    }
}
