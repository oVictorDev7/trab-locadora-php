<?php
namespace App\Model;

//Classe Filme no padrão moderno: construtor privado, factory method estático e validação centralizada.
class Filme{
    //Valores aceitos para a classificação indicativa (idade recomendada).
    const IDADES = ["Livre", "10", "12", "14", "16", "18"];

    //O ? indica que o tipo pode ser nulo.
    private ?int $id;
    private string $titulo;
    private string $genero;
    private int $ano;
    private float $valorLocacao;
    //Duração em minutos.
    private int $duracao;
    private string $idadeRecomendada;
    //Nome do arquivo da imagem salvo em assets/uploads. Pode ser nulo quando o filme não tem capa.
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

    //Factory method com validação de TODOS os campos. O ID pode ser nulo pois é gerado pelo banco.
    public static function criar(?int $id, ?string $titulo, ?string $genero = null, ?int $ano = null, ?float $valorLocacao = null, ?string $duracao = null, ?string $idadeRecomendada = null, ?string $imagem = null): static {
        //Título obrigatório.
        if ($titulo === null || trim($titulo) === "") {
            throw new \InvalidArgumentException("O título é obrigatório");
        }
        //Gênero/categoria obrigatório.
        if ($genero === null || trim($genero) === "") {
            throw new \InvalidArgumentException("Selecione uma categoria/gênero");
        }
        //Ano dentro de uma faixa razoável (do primeiro filme da história até o ano seguinte ao atual).
        $ano = (int) $ano;
        if ($ano < 1888 || $ano > ((int) date("Y") + 1)) {
            throw new \InvalidArgumentException("Informe um ano válido (entre 1888 e " . ((int) date("Y") + 1) . ")");
        }
        //Valor da locação não pode ser negativo.
        $valorLocacao = (float) $valorLocacao;
        if ($valorLocacao < 0) {
            throw new \InvalidArgumentException("O valor da locação não pode ser negativo");
        }
        //Duração em minutos deve ser positiva.
        $duracao = (int) $duracao;
        if ($duracao <= 0) {
            throw new \InvalidArgumentException("Informe a duração em minutos (maior que zero)");
        }
        //Idade recomendada precisa ser uma das opções válidas.
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
