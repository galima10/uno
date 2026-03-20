<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Services\PlayService;

final class PlayController extends AbstractController
{
    #[Route('/play', name: 'play_index')]
    public function index(PlayService $playService): Response
    {
        $data = $playService->initData();
        return $this->render('play/index.html.twig', [
            'deck' => $data['deck'],
            'players' => $data['players'],
            'topDiscardCard' => $data['topDiscardCard'],
            'discard' => $data['discard'],
            'turn' => $data['turn'],
            'enemyCardPlayed' => $data['enemyCardPlayed'],
            'cardPicked' => $data['cardPicked'],
            'accumulation' => $data['accumulation'],
            'sens' => $data['sens'],
            'winner' => $data['winner']
        ]);
    }

        
}
