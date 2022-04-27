<?php

namespace Lee\Tests;

use InvalidArgumentException;
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

        $mock->expects($this->once())
             ->method('track')
             ->willReturn([
                 $url,
                 'https://www.slideshare.net/williamyeh/grpc-238408172/williamyeh/grpc-238408172',
                ]);

        $expected = [
            $url,
            'https://www.slideshare.net/williamyeh/grpc-238408172/williamyeh/grpc-238408172',
        ];
        $result = $mock->track();

        $this->assertEquals($expected, $result);
    }

    public function testGetUrl(): void
    {
        $url = 'https://bit.ly/grpc-intro';
        $tracker = new Tracker($url);

        $this->assertSame($url, $tracker->getUrl());
    }

    public function testGetUrlShouldThrowInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $url = 'I am not valid URL';
        $tracker = new Tracker($url);
        $tracker->track();
    }
}
