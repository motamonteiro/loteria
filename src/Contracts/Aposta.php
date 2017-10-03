<?php

namespace MotaMonteiro\Loteria\Contracts;


interface Aposta
{
    public function adicionarAposta(array $numeros);
    public function gerarApostaSurpresinha():array;
    public function simularSorteio():array;
    public function consultarAcertos():array;

}
