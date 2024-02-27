<?php
declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CalendarFileFetcher
{
    public function __construct(private string $calendarFileUrl, private HttpClientInterface $httpClient)
    {
    }

    private function downloadCalendarFile(): string
    {
        $response = $this->httpClient->request('GET', $this->calendarFileUrl);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception("Failed to fetch file: HTTP Error {$response->getStatusCode()}");
        }

        return $response->getContent();
    }

    private function parseCalendarData(string $fileContent): array
    {

        $fileLines = explode("\r\n", $fileContent);
        $events = [];

        $currentEventData = [];
        foreach ($fileLines as $line) {
            if (empty($line)) {
                continue;
            }

            if ($line === 'END:VEVENT') {
                $events[] = $currentEventData;
                $currentEventData = [];
                continue;
            }

            $lineData = $this->getLineData($line);
            if ($lineData !== null) {
                $currentEventData += $lineData;
            }
        }

        return $events;
    }

    private function getLineData(string $lineData): array|null
    {
        [$key, $value] = explode(':', str_replace("\r\n", '', $lineData));

        return match ($key) {
            'UID' => ['id' => $value],
            'SUMMARY' => ['summary' => $value],
            'DTSTART;VALUE=DATE' => ['start' => $this->createDateFromString($value)->format('Y-m-d')],
            'DTEND;VALUE=DATE' => ['end' => $this->createDateFromString($value)->format('Y-m-d')],
            default => null
        };
    }

    private function createDateFromString(string $dateString): \DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat('Ymd', $dateString)
            ->setTime(0, 0, 0);

        if ($date === false) {
            throw new \InvalidArgumentException('Failed to create date from string');
        }

        return $date;
    }


    public function getCalendarData(): array
    {
        return $this->parseCalendarData($this->downloadCalendarFile());
    }
}