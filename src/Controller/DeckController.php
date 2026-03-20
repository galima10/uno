<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\DeckService;

final class DeckController extends AbstractController
{
    #[Route('/deck/{playerId}', name: 'deck_index', requirements: ['playerId' => '[0123]'])]
    public function index(string $playerId, DeckService $deckService): Response
    {
        $deckService->pickCard(intval($playerId));
        return $this->redirectToRoute('play_index');
    }
}
