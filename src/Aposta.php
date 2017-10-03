<?php

namespace MotaMonteiro\Loteria;

use MotaMonteiro\Loteria\Contracts\Aposta as ApostaInterface;

class Aposta implements ApostaInterface
{
    protected $sorteio;
    protected $apostas;
    protected $qtdNumeroAposta;
    protected $maxNumeroAposta;

    public function __construct(int $qtdNumeroAposta, int $maxNumeroAposta)
    {
        $this->apostas = [];
        $this->sorteio = [];
        $this->qtdNumeroAposta = $qtdNumeroAposta;
        $this->maxNumeroAposta = $maxNumeroAposta;
    }

    public function getSorteio(): array
    {
        return $this->sorteio;
    }

    public function getApostas(): array
    {
        return $this->apostas;
    }

    public function getQtdNumeroAposta(): int
    {
        return $this->qtdNumeroAposta;
    }

    public function getMaxNumeroAposta(): int
    {
        return $this->maxNumeroAposta;
    }

    public function flgNumerosRepetidos(array $numeros)
    {
        foreach ($this->getApostas() as $aposta) {
            $diferenca = array_diff($aposta['numeros'], $numeros);
            if (empty($diferenca)) {
                return true;
            }
        }
        return false;
    }

    public function adicionarAposta(array $numeros): Aposta
    {
        asort($numeros);
        if (!$this->flgNumerosRepetidos($numeros)) {
            $aposta = [
                'identificador' => (count($this->apostas) + 1),
                'numeros' => $numeros,
                'dataAposta' => date('d/m/Y H:i:s'),
                'acertos' => 0,
            ];

            array_push($this->apostas, $aposta);
        }

        return $this;

    }

    public function gerarApostaSurpresinha(): array
    {
        $numeros = range(1, $this->getMaxNumeroAposta());
        shuffle($numeros);
        $numeros = array_slice($numeros, 0, $this->getQtdNumeroAposta());
        asort($numeros);
        return $numeros;
    }

    public function simularSorteio(): array
    {
        $numeros = range(1, $this->getMaxNumeroAposta());
        shuffle($numeros);
        $numeros = array_slice($numeros, 0, $this->getQtdNumeroAposta());
        asort($numeros);
        $this->sorteio = $numeros;
        return $this->sorteio;
    }

    public function consultarAcertos(): array
    {
        if (empty($this->apostas) || empty($this->sorteio)) {
            return [];
        }

        $posicao = 0;
        foreach ($this->apostas as $aposta) {
            $acertos = 0;
            foreach ($aposta['numeros'] as $numero) {
                if (in_array($numero, $this->sorteio)) {
                    $acertos++;
                }
            }

            $this->apostas[$posicao]['acertos'] = $acertos;
            $posicao++;
        }

        return $this->apostas;

    }

}