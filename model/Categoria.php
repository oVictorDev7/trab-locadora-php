<?php
namespace App\Model;

//Classe Categoria no mesmo padrão de Filme e Usuario: construtor privado, factory method e validação nos setters.
class Categoria{
    private ?int $id;
    private string $nome;
    private ?string $descricao;

    private function __construct(?int $id, string $nome, ?string $descricao){
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
    }

    //Factory method com validação do nome obrigatório. O ID pode ser nulo pois é gerado pelo banco.
    public static function criar(?int $id, ?string $nome, ?string $descricao = null): static {
        if ($nome === null || trim($nome) === "") {
            throw new \InvalidArgumentException("O nome da categoria é obrigatório");
        }
        return new static($id, $nome, $descricao);
    }

    public function getId(): ?int { return $this->id; }
    public function getNome(): string { return $this->nome; }
    public function getDescricao(): ?string { return $this->descricao; }

    public function setNome(string $nome): void {
        if (trim($nome) === "") {
            throw new \InvalidArgumentException("O nome da categoria é obrigatório");
        }
        $this->nome = $nome;
    }

    public function setDescricao(?string $descricao): void {
        $this->descricao = $descricao;
    }
}
