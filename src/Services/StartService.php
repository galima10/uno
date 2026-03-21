<?php

namespace App\Services;

use App\Model\CardModel;
use App\Model\PlayerModel;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class StartService
{
  private SessionInterface $session;
  private array $deck = [];
  private array $players = [];

  // Récupérer la session depuis le service
  public function __construct(RequestStack $requestStack)
  {
    $this->session = $requestStack->getSession();
  }

  // Création de 100 cartes
  // 4 couleurs de cartes de 0 à 12 et les cartes de 1 à 12 sont doublées
  private function createDeck()
  {
    // On initialise l'ID des cartes à 1
    $cardId = 1;

    // Pour chaque couleur :
    foreach (['red', 'yellow', 'blue', 'green'] as $color) {
      // On ajoute la carte 0 de cette couleur
      $newCard = new CardModel($cardId, 0, $color);
      $this->deck[] = $newCard;
      $cardId++;

      // On ajouter les cartes de 1 à 12
      for ($i = 1; $i <= 12; $i++) {
        // On double les cartes
        for ($j = 0; $j < 2; $j++) {
          $newCard = new CardModel($cardId, $i, $color);
          $this->deck[] = $newCard;
          $cardId++;
        }
      }
    }
  }

  // Mélange la pioche
  private function mixDeck()
  {
    $deck = $this->deck;
    $mixDeck = [];

    for ($i = 0; $i < count($this->deck); $i++) {
      $randomIndex = array_rand($deck);
      $mixDeck[] = $deck[$randomIndex];
      array_splice($deck, $randomIndex, 1);
    }

    $this->deck = $mixDeck;
  }

  // Crée les joueurs
  private function createPlayers($maxPlayers)
  {
    // Ajouter le joueur utilisateur en premier
    $players = [];
    $user = new PlayerModel(0, 'user', 'Vous');
    $players[] = $user;

    // Créer des numéros d'ordinateurs aléatoires (pour créer un semblant de ce n'est pas toujours le "même" ordre)
    $enemyNumbers = [];
    for ($i = 1; $i < $maxPlayers; $i++) {
      $enemyNumbers[] = $i;
    }

    // Créer les ordinateurs
    for ($i = 0; $i < $maxPlayers - 1; $i++) {
      $randomId = array_rand($enemyNumbers);
      $enemy = new PlayerModel($i + 1, 'enemy', 'Ordinateur ' . $enemyNumbers[$randomId]);
      array_splice($enemyNumbers, $randomId, 1);
      $players[] = $enemy;
    }

    $this->players = $players;
  }

  // Distribue les cartes aux joueurs
  private function distributeCards($number)
  {
    for ($i = 0; $i < count($this->players); $i++) {
      $cards = [];

      for ($j = 0; $j < $number; $j++) {
        $cards[] = $this->deck[0];
        array_shift($this->deck);
      }

      $this->players[$i]->setCards($cards);
    }
  }

  // Pioche une première carte de la pioche pour commencer
  // Si c'est une carte spéciale, on la remet à la fin de la pioche et en repioche une
  private function getFirstCard()
  {
    if (empty($this->deck)) {
      throw new \RuntimeException('Le deck est vide, impossible de tirer une carte.');
    }

    $firstCard = array_shift($this->deck);

    if (in_array($firstCard->getNumber(), [10, 11, 12])) {
      $this->deck[] = $firstCard;

      return $this->getFirstCard();
    }

    return $firstCard;
  }

  // Un joueur tiré au hasard commence
  private function getFirstTurn()
  {
    $randomIndex = array_rand($this->players);
    // return $this->players[$randomIndex];
    return $this->players[0];
  }

  // Lance le jeu et stock les infos en session
  public function initGame()
  {
    // Création de la pioche
    $this->createDeck();
    $this->mixDeck();

    // Création de 4 joueurs auxquels on distribue 7 cartes chacun
    $this->createPlayers(4);
    $this->distributeCards(7);

    $this->session->set('players', $this->players);
    // $this->session->set('discard', [$this->getFirstCard(), $this->getFirstCard()]);
    $this->session->set('discard', [$this->getFirstCard()]);
    $this->session->set('deck', $this->deck);
    $this->session->set('turn', $this->getFirstTurn());
    $this->session->set('enemyCardPlayed', null);
    $this->session->set('cardPicked', false);
    $this->session->set('accumulation', 0);
    $this->session->set('sens', 1);
    $this->session->set('winner', null);
  }
}
