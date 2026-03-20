<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class WinController extends AbstractController
{
    #[Route('/win', name: 'win_index')]
    public function index(SessionInterface $session): Response
    {
        $winner = $session->get('winner');
        return $this->render('win/index.html.twig', [
            'winner' => $winner,
        ]);
    }
}
