<?php
namespace App\Model;

class Usuario{
    private ?int $id;
    private string $nome;
    private string $email;
    private string $senha;
    private string $cpf;
    private string $nascimento;
    private string $tipo;

    private function __construct(?int $id, string $nome, string $email, string $senha, string $cpf, string $nascimento, string $tipo){
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->cpf = $cpf;
        $this->nascimento = $nascimento;
        $this->tipo = $tipo;
    }

    public static function criar(?int $id, ?string $nome, ?string $email, ?string $senha, ?string $cpf, ?string $nascimento, string $tipo = "usuario"): static {
        if ($nome === null || trim($nome) === "") {
            throw new \InvalidArgumentException("O nome é obrigatório");
        }
        if ($email === null || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Informe um e-mail válido");
        }
        if ($senha === null || trim($senha) === "") {
            throw new \InvalidArgumentException("A senha é obrigatória");
        }
        $cpf = preg_replace("/\D/", "", $cpf ?? "");
        if (strlen($cpf) !== 11) {
            throw new \InvalidArgumentException("Informe um CPF válido (11 dígitos)");
        }
        if ($nascimento === null || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $nascimento)) {
            throw new \InvalidArgumentException("Informe uma data de nascimento válida");
        }
        if ($tipo !== "admin" && $tipo !== "usuario") {
            throw new \InvalidArgumentException("Tipo de usuário inválido");
        }
        return new static($id, $nome, $email, $senha, $cpf, $nascimento, $tipo);
    }

    public function getId(): ?int { return $this->id; }
    public function getNome(): string { return $this->nome; }
    public function getEmail(): string { return $this->email; }
    public function getSenha(): string { return $this->senha; }
    public function getCpf(): string { return $this->cpf; }
    public function getNascimento(): string { return $this->nascimento; }
    public function getTipo(): string { return $this->tipo; }

    public function ehAdmin(): bool {
        return $this->tipo === "admin";
    }
}
