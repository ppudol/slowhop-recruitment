<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\CalendarFileFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;

 #[Route('/api', name: 'app_api')]
class ApiController extends AbstractController
{
    public function __construct(private CalendarFileFetcher $calendarFileFetcher, private LoggerInterface $logger)
    {}
    
    #[Route('/get-calendar-data', name: 'app_api_get_calendar_data', methods: ['GET'])]
    public function getCalendarData(): JsonResponse
    {
        $this->logger->info('Fetching calendar data');
        $this->calendarFileFetcher->getCalendarData();

        return $this->json([
            $this->calendarFileFetcher->getCalendarData()
        ]);
    }

}
