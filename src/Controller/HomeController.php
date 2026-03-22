<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'home_index')]
    public function index(SessionInterface $session): Response
    {
        $session->set('globalLeaderBoard', []);
        $session->set('players', []);
        return $this->render('home/index.html.twig');
    }
}
