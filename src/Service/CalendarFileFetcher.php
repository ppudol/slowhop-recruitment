<?php
declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CalendarFileFetcher
{
    public function __construct(private string $calendarFileUrl, private HttpClientInterface $httpClient)
    {}

    public function downloadCalendarFile(): string
    {
        $response = $this->httpClient->request('GET', $this->calendarFileUrl);
        
        if ($response->getStatusCode() !== 200) {   
            throw new \Exception("Failed to fetch file: HTTP Error {$response->getStatusCode()}");
        }

        return $response->getContent();
    }    
}