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
        'img' => 'img1',
      ],
      [
        'name' => 'Emma',
        'img' => 'img2',
      ],
      [
        'name' => 'Noah',
        'img' => 'img3',
      ],
      [
        'name' => 'Léa',
        'img' => 'img4',
      ],
      [
        'name' => 'Hugo',
        'img' => 'img5',
      ],
      [
        'name' => 'Jade',
        'img' => 'img6',
      ],
      [
        'name' => 'Louis',
        'img' => 'img7',
      ],
      [
        'name' => 'Chloé',
        'img' => 'img8',
      ],
      [
        'name' => 'Nathan',
        'img' => 'img9',
      ],
      [
        'name' => 'Manon',
        'img' => 'img10',
      ],
      [
        'name' => 'Enzo',
        'img' => 'img11',
      ],
      [
        'name' => 'Camille',
        'img' => 'img12',
      ],
      [
        'name' => 'Gabriel',
        'img' => 'img13',
      ],
      [
        'name' => 'Sarah',
        'img' => 'img14',
      ],
      [
        'name' => 'Mathis',
        'img' => 'img15',
      ],
    ];
    return $enemies;
  }
}
