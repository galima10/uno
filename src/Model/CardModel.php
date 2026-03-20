<?php

namespace App\Model;

class CardModel {
  private int $id;
  private string $color;
  private int $number;

  public function __construct($newId,$newNumber, $newColor)
  {
    $this->setId($newId);
    $this->setNumber($newNumber);
    $this->setColor($newColor);
  }

  private function setId($newId) {
    if (is_int($newId) && $newId >= 1 && $newId <= 100) $this->id = $newId;
  }

  private function setNumber($newNumber) {
    if (is_int($newNumber) && $newNumber >= 0 && $newNumber <= 12) $this->number = $newNumber;
  }

  private function setColor($newColor) {
    if (is_string($newColor) && in_array($newColor, ['red', 'yellow', 'blue', 'green'])) $this->color = $newColor;
  }

  public function getId() {
    return $this->id;
  }

  public function getNumber() {
    return $this->number;
  }

  public function getColor() {
    return $this->color;
  }
}
