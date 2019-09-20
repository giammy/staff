<?php

// src/Twig/AppExtension.php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions() {
        return [
            new TwigFunction('userBelongsTo', [$this, 'userBelongsTo']),
        ];
    }

    public function userBelongsTo(string $list, string $who) {
      return in_array($who, preg_split('/, */', $list));
    }
}
