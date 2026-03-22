<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\StartService;

final class StartController extends AbstractController
{
    #[Route('/start', name: 'start_index')]
    public function index(StartService $startService): Response
    {
        $startService->initGame();
        return $this->render('start/index.html.twig');
    }
}
