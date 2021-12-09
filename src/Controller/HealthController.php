<?php

namespace App\Controller;

use App\Service\HealthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthController extends AbstractController
{
    #[Route('/health', name: 'health', methods: ["GET"])]
    public function index(HealthService $healthService): Response
    {
        return $this->json([
            'APP_ENV' => $healthService->getHealth()
        ]);
    }
}
