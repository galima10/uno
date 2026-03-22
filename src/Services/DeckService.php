<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DeckService
{
  private SessionInterface $session;

  // Récupérer la session depuis le service
  public function __construct(RequestStack $requestStack)
  {
    $this->session = $requestStack->getSession();
  }

  // On pioche une carte
  public function pickCard(int $playerId)
  {
    // Si le joueur en question (déterminé avec le playerId) a déjà piochée, on arrête la fonction
    if ($this->session->get('cardPicked') !== false) {
      throw new \RuntimeException('Une carte a déjà été piochée pour ce tour.');
    }


    $deck = $this->session->get('deck');
    if (empty($deck)) {
      throw new \RuntimeException('Aucun deck trouvé dans la session.');
    }
    $players = $this->session->get('players');
    if (empty($players)) {
      throw new \RuntimeException('Aucun joueur trouvé dans la session.');
    }
    $accumulation = $this->session->get('accumulation');

    // On fait une copie de la main du joueur qui va être modifiée après la pioche
    $currentCards = array_merge([], $players[$playerId]->getCards());

    // Si il y a de l'accumulation de +2
    if ($accumulation > 0) {
      // On pioche le nombre de carte donnée par l'accumulation
      for ($i = 0; $i < $accumulation; $i++) {
        $currentCards[] = array_shift($deck);
        $players[$playerId]->setCards($currentCards);
        // On mélange le deck s'il est vide après avoir pioché
        $this->remixDeckFromDiscard($deck);
      }
      // On remet à 0 l'accumulation
      $accumulation = 0;

      // On passe direct au tour suivant après avoir pioché
      $this->nextTurn(1);
      $this->session->set('cardPicked', false);
    } 
    // Si le joueur pioche juste une carte car il n'a pas de carte jouable
    else {
      // Il pioche la carte au dessus de la pioche
      $currentCards[] = array_shift($deck);
      $players[$playerId]->setCards($currentCards);

      // On mélange le deck s'il est vide après avoir pioché
      $this->remixDeckFromDiscard($deck);
      $this->session->set('cardPicked', true);
    }

    // On remet à jour les infos en session
    $this->session->set('accumulation', $accumulation);
    $this->session->set('deck', $deck);
    $this->session->set('players', $players);
  }

  // On mélange le deck si il est vide avec les cartes récupérées de la défausse
  private function remixDeckFromDiscard($currentDeck)
  {
    // On vérifie si la pioche est vide ou non
    if (count($currentDeck) > 0) return;

    // On récupère les cartes de la pile de défausse en laissant la dernière carte dans la défausse
    $discard = $this->session->get('discard');
    $onlyTopDiscardCard = [array_pop($discard)];
    $this->session->set('discard', $onlyTopDiscardCard);

    // On mélange la nouvelle pioche
    $mixDeck = $discard;
    shuffle($mixDeck);

    return $mixDeck;
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
