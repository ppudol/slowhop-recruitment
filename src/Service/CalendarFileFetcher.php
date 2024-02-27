<?php
declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class CalendarFileFetcher
{
    public function __construct(private string $calendarFileUrl, private HttpClientInterface $httpClient, private LoggerInterface $logger)
    {
    }

    private function downloadCalendarFile(): string
    {
        $response = $this->httpClient->request('GET', $this->calendarFileUrl);

        if ($response->getStatusCode() !== 200) {
            $this->logger->error('File fetching failed');
            throw new \Exception("Failed to fetch file: HTTP Error {$response->getStatusCode()}");
        }

        return $response->getContent();
    }

    private function parseCalendarData(string $fileContent): array
    {

        $fileLines = array_map('trim', explode(PHP_EOL, $fileContent));
        $events = [];

        $currentEventData = [];
        foreach ($fileLines as $line) {
            if (empty($line)) {
                continue;
            }

            if ($line === 'END:VEVENT') {
                $events[] = $this->changeKeysOrder($currentEventData);
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
        [$key, $value] = explode(':', $lineData);

        return match ($key) {
            'UID' => ['id' => $value],
            'SUMMARY' => ['summary' => $value],
            'DTSTART;VALUE=DATE' => ['start' => $this->createDateFromString($value)->format('Y-m-d')],
            'DTEND;VALUE=DATE' => ['end' => $this->createDateFromString($value)->format('Y-m-d')],
            default => null
        };
    }

    private function changeKeysOrder(array $data): array
    {
        return [
            'id' => $data['id'],
            'start' => $data['start'],
            'end' => $data['end'],
            'summary' => $data['summary'],
        ];
    }

    private function createDateFromString(string $dateString): \DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat('Ymd', $dateString);

        if ($date === false) {
            $this->logger->error('Failed to create date from string');
            throw new \InvalidArgumentException('Failed to create date from string');
        }

        return $date->setTime(0,0,0);
    }


    public function getCalendarData(): array
    {
        return $this->parseCalendarData($this->downloadCalendarFile());
    }
}