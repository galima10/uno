<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\EndService;

final class EndController extends AbstractController
{
    #[Route('/end', name: 'end_index')]
    public function index(EndService $endService): Response
    {
        $result = $endService->getResult();
        return $this->render('end/index.html.twig', [
            'players' => $result['players'],
            'winner' => $result['winner'],
            'actualRoundLeaderBoard' => $result['actualRoundLeaderBoard'],
            'globalLeaderBoard' => $result['globalLeaderBoard'],
        ]);
    }
}
