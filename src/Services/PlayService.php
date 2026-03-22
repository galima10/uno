<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PlayService
{
  private SessionInterface $session;
  private array $deck = [];
  private array $players = [];
  private array $discard = [];
  private $turn;
  private $topDiscardCard;
  private $enemyCardPlayed;
  private $cardPicked;
  private int $accumulation;
  private int $sens;

  // Récupérer la session depuis le service
  public function __construct(RequestStack $requestStack)
  {
    $this->session = $requestStack->getSession();
  }

  // On récupère toutes les données stockées dans la session du StartController
  public function initData()
  {
    $this->deck = $this->session->get('deck');
    $this->players = $this->session->get('players');
    $this->discard = $this->session->get('discard');
    $this->turn = $this->session->get('turn');
    $this->cardPicked = $this->session->get('cardPicked');
    $this->accumulation = $this->session->get('accumulation');
    $this->sens = $this->session->get('sens');
    $this->topDiscardCard = end($this->discard);
    $this->resolveTurn($this->turn, $this->topDiscardCard, $this->cardPicked);
    $cardAngle = $this->setRandomAngleByPlayer($this->turn, $this->topDiscardCard);
    $this->session->set('actualCardAngle', $cardAngle);

    return [
      'deck' => $this->deck,
      'players' => $this->players,
      'discard' => $this->discard,
      'turn' => $this->turn,
      'topDiscardCard' => $this->topDiscardCard,
      'enemyCardPlayed' => $this->enemyCardPlayed,
      'cardPicked' => $this->cardPicked,
      'accumulation' => $this->accumulation,
      'sens' => $this->sens,
      'winner' => $this->checkWinner($this->players),
      'cardAngle' => $cardAngle,
    ];
  }

  private function setRandomAngleByPlayer($turn, $topDiscardCard)
  {
    $randomAngle = rand(-5, 5) * 2;
    return $randomAngle;
  }

  // On regarde si il y a un gagnant (si un joueur n'a plus de cartes)
  private function checkWinner($players)
  {
    $winner = null;

    foreach ($players as $player) {
      if (count($player->getCards()) === 0) {
        $winner = $player;
        break;
      }
    }

    if ($winner) {
      $this->session->set('winner', $winner);
      $this->session->set('endStatus', 'finish');
    };
    return $winner;
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

  // Gestion du tour (quel joueur est en train de jouer, et que fait-on)
  // => Gère aussi l'action après avoir pioché une carte
  // Il y a des étapes intermédiaires pour gérer l'animation (éviter que ce soit instantané)
  private function resolveTurn($turn, $topDiscardCard, $cardPicked)
  {
    if (!$topDiscardCard) {
      throw new \RuntimeException('La pile de défausse est vide ou invalide.');
    }

    // Si c'est le tour du joueur user
    if ($turn->getPlayerType() === 'user') {
      $useCards = $this->getValidCard($turn, $topDiscardCard);


      // On regarde bien si le joueur utilisateur ne peut vraiment plus jouer après avoir piocher
      if ($cardPicked) {
        if (empty($useCards)) {
          $cardPicked = null;
          $this->session->set('cardPicked', null);
        }
      }
      // Étape intermédiaire pour laisser le temps à l'utilisateur de voir l'action
      else if ($cardPicked === null) {
        $this->nextTurn(1);
        $cardPicked = false;
        $this->session->set('cardPicked', false);
      }
    }
    // Si c'est le tour d'un ordinateur
    else {
      // Carte aléatoire jouable de l'ordinateur
      $enemyCard = $this->getEnemyCardPlayed($turn, $topDiscardCard);

      // On regarde bien si l'ordinateur ne peut vraiment plus jouer après avoir piocher
      if ($cardPicked) {
        if (empty($enemyCard)) {
          $this->session->set('cardPicked', null);
        }
      }
      // Étape intermédiaire pour laisser le temps à l'utilisateur de voir l'action
      elseif ($cardPicked === null) {
        if (empty($enemyCard)) {
          $this->session->set('cardPicked', false);
          $this->nextTurn(1);
        }
      }
      // Stockage de la carte jouée par l'ordinateur (même si elle est null, pour gérer l'affichage UI et les timer)
      $this->enemyCardPlayed = $enemyCard;
    }
  }

  // Récupère toutes les cartes jouables pour ce tour
  private function getValidCard($player, $topDiscardCard)
  {
    $accumulation = $this->accumulation;
    $playerCards = [];

    foreach ($player->getCards() as $card) {
      if ($accumulation > 0) {
        if ($topDiscardCard->getNumber() === 12 && $topDiscardCard->getNumber() === $card->getNumber()) {
          $playerCards[] = $card;
        }
      } else {
        if ($topDiscardCard->getNumber() === $card->getNumber() || $topDiscardCard->getColor() === $card->getColor()) {
          $playerCards[] = $card;
        }
      }
    }

    if (count($playerCards) === 0) return;
    return $playerCards;
  }

  // Tirage aléatoire d'une carte jouable qui va être jouée par l'ordinateur
  private function getEnemyCardPlayed($enemy, $topDiscardCard)
  {
    $enemyCards = $this->getValidCard($enemy, $topDiscardCard);

    if (!empty($enemyCards)) {
      $randomCardValid = $enemyCards[array_rand($enemyCards)];
      $this->session->set('enemyCardPlayed', $randomCardValid);

      return $randomCardValid;
    } else return;
  }
}
