<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\PlayerService;

final class PlayerController extends AbstractController
{
    #[Route('/player/{cardId}', name: 'user_index', requirements: ['cardId' => '^(?:[1-9]|[1-9][0-9]|100)$'])]
    public function user(string $cardId, PlayerService $playerService): Response
    {
        $playerService->checkUserCard(intval($cardId));
        return $this->redirectToRoute('play_index');
    }

    #[Route('/enemy/{enemyId}', name: 'enemy_index', requirements: ['enemyId' => '[123]'])]
    public function enemy(string $enemyId, PlayerService $playerService): Response
    {
        $playerService->checkEnemyCard(intval($enemyId));
        return $this->redirectToRoute('play_index');
    }
}
