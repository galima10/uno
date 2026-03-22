<?php

namespace App\Model;

class CardModel
{
  private int $id;
  private string $color;
  private int $number;

  // Image de la carte
  private string $img;

  // Ajout d'un angle pour l'UI
  private int $angle;

  public function __construct($newId, $newNumber, $newColor)
  {
    $this->setId($newId);
    $this->setNumber($newNumber);
    $this->setColor($newColor);
    $this->setImg($newColor, $newNumber);
  }

  private function setId($newId)
  {
    if (is_int($newId) && $newId >= 1 && $newId <= 100) $this->id = $newId;
  }
  private function setImg($color, $number)
  {
    if (is_int($number) && $number >= 0 && $number <= 12 && is_string($color) && in_array($color, ['red', 'yellow', 'blue', 'green'])) $this->img = sprintf('assets/images/cards/%s_%d.webp', $color, $number);
    else $this->img = 'assets/images/cards/back.webp'; // dos de la carte par défaut
  }

  private function setNumber($newNumber)
  {
    if (is_int($newNumber) && $newNumber >= 0 && $newNumber <= 12) $this->number = $newNumber;
  }

  private function setColor($newColor)
  {
    if (is_string($newColor) && in_array($newColor, ['red', 'yellow', 'blue', 'green'])) $this->color = $newColor;
  }

  public function setAngle($newAngle)
  {
    if (is_int($newAngle)) $this->angle = $newAngle;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getNumber()
  {
    return $this->number;
  }

  public function getColor()
  {
    return $this->color;
  }

  public function getImg()
  {
    return $this->img;
  }

  public function getAngle()
  {
    return $this->angle;
  }
}
