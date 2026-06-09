<?php
namespace App\Model;

class Mensagem{
    private ?int $id;
    private string $nome;
    private string $email;
    private string $mensagem;
    private ?string $dataEnvio;

    private function __construct(?int $id, string $nome, string $email, string $mensagem, ?string $dataEnvio){
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->mensagem = $mensagem;
        $this->dataEnvio = $dataEnvio;
    }

    public static function criar(?int $id, ?string $nome, ?string $email, ?string $mensagem, ?string $dataEnvio = null): static {
        if ($nome === null || trim($nome) === "") {
            throw new \InvalidArgumentException("Informe o seu nome");
        }
        if ($email === null || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Informe um e-mail válido");
        }
        if ($mensagem === null || trim($mensagem) === "") {
            throw new \InvalidArgumentException("Escreva a sua mensagem");
        }
        return new static($id, $nome, $email, $mensagem, $dataEnvio);
    }

    public function getId(): ?int { return $this->id; }
    public function getNome(): string { return $this->nome; }
    public function getEmail(): string { return $this->email; }
    public function getMensagem(): string { return $this->mensagem; }
    public function getDataEnvio(): ?string { return $this->dataEnvio; }
}
