<?php

namespace App\Model;

class PlayerModel {
  private int $id;
  private string $playerType;
  private string $name;
  private string $img;
  private array $cards = [];

  public function __construct($newId, $newPlayerType, $newName, $newImg)
  {
    $this->setId($newId);
    $this->setPlayerType($newPlayerType);
    $this->setName($newName);
    $this->setImg($newImg);
  }

  public function setId($newId) {
    if (is_int($newId) && $newId >= 0 && $newId <= 3) $this->id = $newId;
  }

  private function setPlayerType($newPlayerType) {
    if (is_string($newPlayerType) && in_array($newPlayerType, ['enemy', 'user'])) $this->playerType = $newPlayerType;
  }

  private function setName($newName) {
    if (is_string($newName) && strlen($newName) > 0 && strlen($newName) <= 20) $this->name = $newName;
  }

  private function setImg($newImg) {
    if (is_string($newImg) && strlen($newImg) > 0 && strlen($newImg) <= 255) $this->img = $newImg;
  }

  public function setCards($newCards) {
    if (is_array($newCards) && count($newCards) >= 0 && count($newCards) <= 100) $this->cards = $newCards;
  }

  public function getId() {
    return $this->id;
  }
  public function getPlayerType() {
    return $this->playerType;
  }
  public function getName() {
    return $this->name;
  }
  public function getImg() {
    return $this->img;
  }
  public function getCards() {
    return $this->cards;
  }
}