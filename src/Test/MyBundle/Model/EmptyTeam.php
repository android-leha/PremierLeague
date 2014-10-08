<?php

namespace Test\MyBundle\Model;


class EmptyTeam extends Team
{
    public function __construct()
    {
        parent::__construct('Empty', 0);
    }
} 