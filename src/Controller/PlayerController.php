<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\PlayerService;

final class PlayerController extends AbstractController
{
    #[Route('/player/{cardId}-{cardAngle}', name: 'user_index', requirements: ['cardId' => '^(?:[1-9]|[1-9][0-9]|100)$', 'cardAngle' => '^-?(?:10|[0-9])$'])]
    public function user(string $cardId, PlayerService $playerService, int $cardAngle): Response
    {
        $playerService->checkUserCard(intval($cardId), intval($cardAngle));
        return $this->redirectToRoute('play_index');
    }

    #[Route('/enemy/{enemyId}-{cardAngle}', name: 'enemy_index', requirements: ['enemyId' => '[123]', 'cardAngle' => '^-?(?:10|[0-9])$'])]
    public function enemy(string $enemyId, PlayerService $playerService, int $cardAngle): Response
    {
        $playerService->checkEnemyCard(intval($enemyId), intval($cardAngle));
        return $this->redirectToRoute('play_index');
    }
}
