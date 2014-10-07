<?php

namespace Test\MyBundle\Model;


use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class Score
 * @property int gd
 * @package Test\MyBundle\Model
 */
class Score
{
    public $pts = 0;
    public $p = 0;
    public $w = 0;
    public $d = 0;
    public $l = 0;
    public $gf = 0;
    public $ga = 0;

    public function __get($name)
    {
        if ($name == 'gd') {
            return $this->gf - $this->ga;
        } else {
            throw new \Exception('Bad variable');
        }
    }


}