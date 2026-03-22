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
        'name' => 'Ordinateur 1',
        'img' => 'url1',
      ],
      [
        'name' => 'Ordinateur 2',
        'img' => 'url2',
      ],
      [
        'name' => 'Ordinateur 3',
        'img' => 'url3',
      ],
      [
        'name' => 'Ordinateur 4',
        'img' => 'url4',
      ],
      [
        'name' => 'Ordinateur 5',
        'img' => 'url5',
      ],
    ];
    return $enemies;
  }
}
