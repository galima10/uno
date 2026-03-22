<?php

namespace App\Model;

class Enemies
{
  private int $id;
  private string $color;
  private int $number;

  public function getEnemyData()
  {
    $enemies = [
      [
        'name' => 'Lucas',
        'img' => 'images/players/lucas.webp',
      ],
      [
        'name' => 'Emma',
        'img' => 'images/players/emma.webp',
      ],
      [
        'name' => 'Noah',
        'img' => 'images/players/noah.webp',
      ],
      [
        'name' => 'Léa',
        'img' => 'images/players/lea.webp',
      ],
      [
        'name' => 'Hugo',
        'img' => 'images/players/hugo.webp',
      ],
      [
        'name' => 'Jade',
        'img' => 'images/players/jade.webp',
      ],
      [
        'name' => 'Louis',
        'img' => 'images/players/louis.webp',
      ],
      [
        'name' => 'Chloé',
        'img' => 'images/players/chloe.webp',
      ],
      [
        'name' => 'Nathan',
        'img' => 'images/players/nathan.webp',
      ],
      [
        'name' => 'Manon',
        'img' => 'images/players/manon.webp',
      ],
      [
        'name' => 'Enzo',
        'img' => 'images/players/enzo.webp',
      ],
      [
        'name' => 'Camille',
        'img' => 'images/players/camille.webp',
      ],
      [
        'name' => 'Gabriel',
        'img' => 'images/players/gabriel.webp',
      ],
      [
        'name' => 'Sarah',
        'img' => 'images/players/sarah.webp',
      ],
      [
        'name' => 'Mathis',
        'img' => 'images/players/mathis.webp',
      ],
    ];
    return $enemies;
  }
}
