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
        'img' => 'assets/images/players/lucas.webp',
      ],
      [
        'name' => 'Emma',
        'img' => 'assets/images/players/emma.webp',
      ],
      [
        'name' => 'Noah',
        'img' => 'assets/images/players/noah.webp',
      ],
      [
        'name' => 'Léa',
        'img' => 'assets/images/players/lea.webp',
      ],
      [
        'name' => 'Hugo',
        'img' => 'assets/images/players/hugo.webp',
      ],
      [
        'name' => 'Jade',
        'img' => 'assets/images/players/jade.webp',
      ],
      [
        'name' => 'Louis',
        'img' => 'assets/images/players/louis.webp',
      ],
      [
        'name' => 'Chloé',
        'img' => 'assets/images/players/chloe.webp',
      ],
      [
        'name' => 'Nathan',
        'img' => 'assets/images/players/nathan.webp',
      ],
      [
        'name' => 'Manon',
        'img' => 'assets/images/players/manon.webp',
      ],
      [
        'name' => 'Enzo',
        'img' => 'assets/images/players/enzo.webp',
      ],
      [
        'name' => 'Camille',
        'img' => 'assets/images/players/camille.webp',
      ],
      [
        'name' => 'Gabriel',
        'img' => 'assets/images/players/gabriel.webp',
      ],
      [
        'name' => 'Sarah',
        'img' => 'assets/images/players/sarah.webp',
      ],
      [
        'name' => 'Mathis',
        'img' => 'assets/images/players/mathis.webp',
      ],
    ];
    return $enemies;
  }
}
