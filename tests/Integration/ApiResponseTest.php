<?php

//declare(strict_types=1);


namespace App\Tests\Integration;

use App\Service\CalendarFileFetcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ApiResponseTest extends TestCase
{
    public function testParseResponse()
    {
        $mockResponse =
        "BEGIN:VCALENDAR
        PRODID:-//eluceo/ical//2.0/EN
        VERSION:2.0
        CALSCALE:GREGORIAN
        BEGIN:VEVENT
        UID:b056d561bed8090cb0249a482065c59e@slowhop.com
        DTSTAMP:20240226T110146Z
        SUMMARY:Blokada - 677125 finalized
        DTSTART;VALUE=DATE:20250101
        DTEND;VALUE=DATE:20250327
        END:VEVENT
        BEGIN:VEVENT
        UID:d3496e69d5c2a2b5ce6792962d2819b4@slowhop.com
        DTSTAMP:20240226T110146Z
        SUMMARY:Blokada - 777166 finalized
        DTSTART;VALUE=DATE:20240328
        DTEND;VALUE=DATE:20240331
        END:VEVENT";

        $expectedResponse = [
            [
                'id' => "b056d561bed8090cb0249a482065c59e@slowhop.com",
                'start' => "2025-01-01",
                'end' => "2025-03-27",
                'summary' => "Blokada - 677125 finalized"
            ],
            [
                'id' => "d3496e69d5c2a2b5ce6792962d2819b4@slowhop.com",
                'start' => "2024-03-28",
                'end' => "2024-03-31",
                'summary' => "Blokada - 777166 finalized"
            ],
        ];

        $httpClient = new MockHttpClient([
            new MockResponse($mockResponse, ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']])
        ]);

        $stub = $this
            ->getMockBuilder(CalendarFileFetcher::class)
            ->setConstructorArgs(['https://slowhop.com/icalendar-export/api-v1/21c0ed902d012461d28605cdb2a8b7a2.ics', $httpClient])
            ->onlyMethods([])
            ->getMock()
        ;

        $response = $stub->getCalendarData();
        $this->assertEquals($response, $expectedResponse);
    }

    public function testCouldNotGetFileException()
    {
        $this->expectException(\Exception::class);

        $httpClient = new MockHttpClient([
            new MockResponse([], ['http_code' => 400, 'response_headers' => ['Content-Type: application/json']])
        ]);

        $stub = $this
            ->getMockBuilder(CalendarFileFetcher::class)
            ->setConstructorArgs(['https://slowhop.com/icalendar-export/api-v1/21c0ed902d012461d28605cdb2a8b7a2.ics', $httpClient])
            ->onlyMethods([])
            ->getMock();

        $stub->getCalendarData();
    }

    public function testDateParseException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $mockResponse =
        "BEGIN:VCALENDAR
        PRODID:-//eluceo/ical//2.0/EN
        VERSION:2.0
        CALSCALE:GREGORIAN
        BEGIN:VEVENT
        UID:b056d561bed8090cb0249a482065c59e@slowhop.com
        DTSTAMP:20240226T110146Z
        SUMMARY:Blokada - 677125 finalized
        DTSTART;VALUE=DATE:wrong_date_format
        DTEND;VALUE=DATE:20250327
        END:VEVENT
        ";

        $httpClient = new MockHttpClient([
            new MockResponse($mockResponse, ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']])
        ]);

        $stub = $this
            ->getMockBuilder(CalendarFileFetcher::class)
            ->setConstructorArgs(['https://slowhop.com/icalendar-export/api-v1/21c0ed902d012461d28605cdb2a8b7a2.ics', $httpClient])
            ->onlyMethods([])
            ->getMock();

        $stub->getCalendarData();
    }
}