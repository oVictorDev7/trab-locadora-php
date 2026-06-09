<?php
namespace App\Model;

class Filme{
    const IDADES = ["Livre", "10", "12", "14", "16", "18"];

    private ?int $id;
    private string $titulo;
    private string $genero;
    private int $ano;
    private float $valorLocacao;
    private int $duracao;
    private string $idadeRecomendada;
    private ?string $imagem;

    private function __construct(?int $id, string $titulo, string $genero, int $ano, float $valorLocacao, int $duracao, string $idadeRecomendada, ?string $imagem){
        $this->id = $id;
        $this->titulo = $titulo;
        $this->genero = $genero;
        $this->ano = $ano;
        $this->valorLocacao = $valorLocacao;
        $this->duracao = $duracao;
        $this->idadeRecomendada = $idadeRecomendada;
        $this->imagem = $imagem;
    }

    public static function criar(?int $id, ?string $titulo, ?string $genero = null, ?int $ano = null, ?float $valorLocacao = null, ?string $duracao = null, ?string $idadeRecomendada = null, ?string $imagem = null): static {
        if ($titulo === null || trim($titulo) === "") {
            throw new \InvalidArgumentException("O título é obrigatório");
        }
        if ($genero === null || trim($genero) === "") {
            throw new \InvalidArgumentException("Selecione uma categoria/gênero");
        }
        $ano = (int) $ano;
        if ($ano < 1888 || $ano > ((int) date("Y") + 1)) {
            throw new \InvalidArgumentException("Informe um ano válido (entre 1888 e " . ((int) date("Y") + 1) . ")");
        }
        $valorLocacao = (float) $valorLocacao;
        if ($valorLocacao < 0) {
            throw new \InvalidArgumentException("O valor da locação não pode ser negativo");
        }
        $duracao = (int) $duracao;
        if ($duracao <= 0) {
            throw new \InvalidArgumentException("Informe a duração em minutos (maior que zero)");
        }
        if (!in_array($idadeRecomendada, self::IDADES, true)) {
            throw new \InvalidArgumentException("Idade recomendada inválida");
        }
        return new static($id, $titulo, $genero, $ano, $valorLocacao, $duracao, $idadeRecomendada, $imagem);
    }

    public function getId(): ?int { return $this->id; }
    public function getTitulo(): string { return $this->titulo; }
    public function getGenero(): string { return $this->genero; }
    public function getAno(): int { return $this->ano; }
    public function getValorLocacao(): float { return $this->valorLocacao; }
    public function getDuracao(): int { return $this->duracao; }
    public function getIdadeRecomendada(): string { return $this->idadeRecomendada; }
    public function getImagem(): ?string { return $this->imagem; }

    public function setImagem(?string $imagem): void {
        $this->imagem = $imagem;
    }
}
