<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

 #[Route('/api', name: 'app_api')]
class ApiController extends AbstractController
{
    public function __construct()
    {}
    
    #[Route('/get-calendar-data', name: 'app_api_get_calendar_data', methods: ['GET'])]
    public function getCalendarData(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }

}
