<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PlayerService
{
  private SessionInterface $session;

  // Récupérer la session depuis le service
  public function __construct(RequestStack $requestStack)
  {
    $this->session = $requestStack->getSession();
  }

  // Route /user : Vérifie la carte jouée par le joueur utilisateur et met à jour le jeu
  public function checkUserCard(int $cardId, int $cardAngle)
  {
    $user = $this->getCurrentPlayer('user');
    $discard = $this->getDiscard();
    $topCard = end($discard);
    $userCard = $this->getUserCard($cardId, $user);

    $this->playCardIfValid($user, $topCard, $userCard, $discard, $cardAngle);

    $this->session->set('enemyCardPlayed', null);
    $this->session->set('cardPicked', false);
  }

  // Route /enemy : Vérifie la carte jouée par l'ordinateur et met à jour le jeu
  public function checkEnemyCard(int $enemyId, int $cardAngle)
  {
    $enemyCard = $this->session->get('enemyCardPlayed');
    $enemy = $this->getCurrentEnemy($enemyId);
    $discard = $this->getDiscard();
    $topCard = end($discard);

    $this->playCardIfValid($enemy, $topCard, $enemyCard, $discard, $cardAngle);

    $this->session->set('enemyCardPlayed', null);
    $this->session->set('cardPicked', false);
  }

  // Récupère le joueur utilisateur dans la liste des joueurs (appelée dans checkUserCard)
  private function getCurrentPlayer()
  {
    $players = $this->session->get('players');

    if (empty($players)) {
      throw new \RuntimeException('Aucun joueur trouvé dans la session.');
    }

    $user = null;

    foreach ($players as $player) {
      if ($player->getPlayerType() === 'user') {
        $user = $player;
        break;
      }
    }

    return $user;
  }

  // Récupère l'ordinateur en fonction de son ID (appelée dans checkEnemyCard)
  private function getCurrentEnemy(int $id)
  {
    $players = $this->session->get('players');

    if (empty($players)) {
      throw new \RuntimeException('Aucun joueur trouvé dans la session.');
    }

    $enemy = null;

    foreach ($players as $player) {
      if ($player->getId() === $id) {
        $enemy = $player;
        break;
      }
    }

    return $enemy;
  }

  // Récupère la pile de défausse
  private function getDiscard()
  {
    $discard = $this->session->get('discard');

    if (empty($discard)) {
      throw new \RuntimeException('La pile de défausse est vide.');
    }

    return $discard;
  }

  // Récupère la carte jouée par le joueur utilisateur (appelée dans checkUserCard)
  private function getUserCard(int $id, $player)
  {
    $userCards = $player->getCards();

    // On récupère la carte avec un filtre
    $currentCard = array_filter($userCards, fn($card) => $card->getId() === $id);
    // On ne prend que la première trouvée (sécurité)
    $currentCard = reset($currentCard);

    return $currentCard;
  }

  // Met à jour la main et la défausse une fois la carte jouée
  private function updatePlayerCards($player, $currentCard, $discard, $cardAngle)
  {
    $userCards = $player->getCards();

    $userCards = array_filter($userCards, fn($card) => $card->getId() !== $currentCard->getId());
    $player->setCards($userCards);
    $currentCard->setAngle(0);
    if ($player->getId() === 0) {
      $currentCard->setAngle($cardAngle);
    } else if ($player->getId() === 1) {
      $currentCard->setAngle(90 + $cardAngle);
    } else if ($player->getId() === 2) {
      $currentCard->setAngle(180 + $cardAngle);
    } else {
      $currentCard->setAngle(270 + $cardAngle);
    }
    $discard[] = $currentCard;

    $this->session->set('discard', $discard);
  }

  // Vérifie la carte jouée (si elle est valide et si elle est une carte spéciale)
  private function playCardIfValid($player, $topCard, $currentCard, $discard, $cardAngle)
  {
    if (!$currentCard) {
      throw new \RuntimeException('La carte est introuvable.');
    }

    // Récupère l'accumulation de +2
    $accumulation = $this->session->get('accumulation');

    // Si il y a eu des +2 accumulés
    if ($accumulation > 0) {
      // La carte jouée ne peut être qu'un autre +2 sur la dernière carte de la pile de défausse, si cette dernière est un +2
      if ($topCard->getNumber() === 12 && $currentCard && ($topCard->getNumber() === $currentCard->getNumber())) {
        $this->updatePlayerCards($player, $currentCard, $discard, $cardAngle);
      } else {
        throw new \RuntimeException('La carte est invalide.');
      }
    } 
    // Si il n'y a pas de +2 accumulés
    else {
      // La carte jouée peut être soit le même nombre que la dernière carte de la pile de défausse ou la même couleur
      if ($currentCard && ($topCard->getColor() === $currentCard->getColor() || $topCard->getNumber() === $currentCard->getNumber())) {
        $this->updatePlayerCards($player, $currentCard, $discard, $cardAngle);
      } else {
        throw new \RuntimeException('La carte est invalide.');
      }
    }

    // Si la carte jouée est un +2, on accumule
    if ($currentCard->getNumber() === 12) {
      $accumulation = $accumulation + 2;
      $this->session->set('accumulation', $accumulation);
    }

    // Si la carte jouée est un changement de sens, on inverse le sens avec * (-1)
    if ($currentCard->getNumber() === 11) {
      $sens = $this->session->get('sens');
      $sens = $sens * (-1);
      $this->session->set('sens', $sens);
    }

    // Si la carte jouée est un saut de tour, on saute un tour
    if ($currentCard->getNumber() === 10) {
      $this->nextTurn(2);
    } 
    // Pour toutes les cartes autres que saut de tour, on passe juste au tour suivant
    else {
      $this->nextTurn(1);
    }
  }

  // Logique du tour suivant
  private function nextTurn($delta)
  {
    $players = $this->session->get('players');

    if (empty($players)) {
      throw new \RuntimeException('Aucun joueur trouvé dans la session.');
    }
    
    $turn = $this->session->get('turn');

    if (empty($turn)) {
      throw new \RuntimeException('Aucun tour trouvé dans la session.');
    }

    $sens = $this->session->get('sens') ?? 1;

    // Index actuel
    $playersIndexTurn = array_search($turn, $players);

    // On récupère le reste de la division entière avec % pour avoir le bon index : si $playersIndexTurn <= count($players), le reste = $playersIndexTurn, quand $playersIndexTurn > count($players) - 1, le reste reboucle à 0, si $playersIndexTurn < 0, le reste est le même que si la division était entre 2 nombres positifs
    $newIndex = ($playersIndexTurn + $delta * $sens + count($players)) % count($players);

    $newTurn = $players[$newIndex];
    $this->session->set('turn', $newTurn);
  }
}
