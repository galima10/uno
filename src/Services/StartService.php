<?php

namespace App\Services;

use App\Model\CardModel;
use App\Model\PlayerModel;
use App\Model\Enemies;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class StartService
{
  private SessionInterface $session;
  private array $allEnemies;
  private array $deck = [];
  private array $players = [];

  // Récupérer la session depuis le service
  public function __construct(RequestStack $requestStack, Enemies $enemiesData)
  {
    $this->session = $requestStack->getSession();
    $this->allEnemies = $enemiesData->getEnemyData();
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
    $mixDeck = $this->deck;
    shuffle($mixDeck);
    $this->deck = $mixDeck;
  }

  private function enemyData($allEnemies)
  {
    $currentAllEnemies = $allEnemies;
    $enemies = [];
    for ($i = 0; $i < 3; $i++) {
      $randomIndex = array_rand($currentAllEnemies);
      $enemies[] = $currentAllEnemies[$randomIndex];
      array_splice($currentAllEnemies, $randomIndex, 1);
    }
    return $enemies;
  }

  // Crée les joueurs s'ils n'existent pas
  private function createPlayers($maxPlayers, $enemies)
  {
    $userImgPath = 'images/players/user.webp';
    // Ajouter le joueur utilisateur en premier
    $players = [];
    $user = new PlayerModel(0, 'user', 'Vous', $userImgPath);
    $players[] = $user;

    // Créer des numéros d'ordinateurs aléatoires (pour créer un semblant de ce n'est pas toujours le "même" ordre)
    $enemyNumbers = [];
    for ($i = 1; $i < $maxPlayers; $i++) {
      $enemyNumbers[] = $i;
    }

    // Créer les ordinateurs
    for ($i = 0; $i < count($enemies); $i++) {
      $randomId = array_rand($enemyNumbers);
      $enemy = new PlayerModel($i + 1, 'enemy', $enemies[$i]['name'], $enemies[$i]['img']);
      array_splice($enemyNumbers, $randomId, 1);
      $players[] = $enemy;
    }
    $this->players = $players;
    $this->session->set('players', $this->players);
  }

  // Mélanger l'ordre des joueurs existants si ils existent déjà quand on reset la partie
  private function mixCurrentPlayers($players)
  {
    $allPlayers = $players;
    $newPlayers = [array_shift($allPlayers)];
    shuffle($allPlayers);
    for ($i = 0; $i < count($allPlayers) ; $i++) {
      $allPlayers[$i]->setId($i + 1);
      $newPlayers[] = $allPlayers[$i];
    }
    $this->players = $newPlayers;
    $this->session->set('players', $this->players);
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

    $firstCard->setAngle(0);

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

    // Vérifier si les joueurs existent déjà dans la session
    $this->players = $this->session->get('players');
    if (empty($this->players)) {
      // Si les joueurs n'existent pas, les créer
      $this->createPlayers(4, $this->enemyData($this->allEnemies), []);
    } else {
      // Sinon les mélanger
      $this->mixCurrentPlayers($this->players);
    }

    // Réinitialiser les données de jeu
    $this->distributeCards(7);
    $this->session->set('discard', [$this->getFirstCard()]);
    $this->session->set('deck', $this->deck);
    $this->session->set('turn', $this->getFirstTurn());
    $this->session->set('enemyCardPlayed', null);
    $this->session->set('cardPicked', false);
    $this->session->set('accumulation', 0);
    $this->session->set('sens', 1);
    $this->session->set('winner', null);
    $this->session->set('actualCardAngle', null);
    $this->session->set('leaderBoard', []);
  }
}
