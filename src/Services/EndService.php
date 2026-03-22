<?php

namespace App\Services;

use App\Model\CardModel;
use App\Model\PlayerModel;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class EndService
{
  private SessionInterface $session;

  // Récupérer la session depuis le service
  public function __construct(RequestStack $requestStack)
  {
    $this->session = $requestStack->getSession();
  }

  public function getResult()
  {

    $players = $this->session->get('players');
    $winner = $this->session->get('winner');
    $endStatus = $this->session->get('endStatus');
    $actualRoundLeaderBoard = $this->getActualLeaderBoard($players);
    $globalLeaderBoard = $this->getGlobalLeaderBoard($actualRoundLeaderBoard, $endStatus);

    return [
      'players' => $players,
      'winner' => $winner,
      'actualRoundLeaderBoard' => $actualRoundLeaderBoard,
      'globalLeaderBoard' => $globalLeaderBoard,
    ];
  }

  private function getGlobalLeaderBoard($actualRoundLeaderBoard, $endStatus)
  {
    $globalLeaderBoard = $this->session->get('globalLeaderBoard') ?? [];

    if ($endStatus === 'finish') {
      if (count($globalLeaderBoard) === 0) {
        $globalLeaderBoard = $actualRoundLeaderBoard;
      } else {
        // Ajoute le scrore de la partie au classement global
        foreach ($actualRoundLeaderBoard as $roundPlayer) {
          $found = false;

          foreach ($globalLeaderBoard as &$globalPlayer) {
            if ($globalPlayer['id'] === $roundPlayer['id']) {
              $globalPlayer['score'] += $roundPlayer['score'];
              $found = true;
              break;
            }
          }

          if (!$found) {
            $globalLeaderBoard[] = $roundPlayer;
          }
        }
      }

      // On remet dans l'ordre le classement final
      usort($globalLeaderBoard, function ($a, $b) {
        return $b['score'] <=> $a['score'];
      });
      $this->session->set('endStatus', null);
    }
    $this->session->set('globalLeaderBoard', $globalLeaderBoard);
    return $globalLeaderBoard;
  }

  private function getActualLeaderBoard($players)
  {
    $actualRoundLeaderBoard = [];

    // Trier les joueurs (du moins de cartes au plus de cartes)
    usort($players, function ($a, $b) {
      $cardsA = count($a->getCards());
      $cardsB = count($b->getCards());
      return $cardsA <=> $cardsB;
    });

    // Points à distribuer
    $points = [150, 100, 50, 0];

    foreach ($players as $index => $player) {
      $actualRoundLeaderBoard[] = [
        'id' => $player->getId(),
        'name' => $player->getName(),
        'score' => $points[$index] ?? 0,
        'img' => $player->getImg()
      ];
    }

    return $actualRoundLeaderBoard;
  }
}
