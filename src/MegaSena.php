<?php

namespace MotaMonteiro\Loteria;


class MegaSena extends Aposta
{
    public function __construct()
    {
        parent::__construct(6, 60);
    }

}