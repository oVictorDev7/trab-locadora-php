<?php
namespace App\Model;

//Locação: relaciona um usuário a um filme alugado. Mesmo padrão dos demais models.
//Os campos usuarioNome e filmeTitulo são preenchidos apenas nas listagens (via JOIN) para exibição.
class Locacao{
    private ?int $id;
    private int $usuarioId;
    private int $filmeId;
    private ?string $dataLocacao;
    private ?string $usuarioNome;
    private ?string $filmeTitulo;

    private function __construct(?int $id, int $usuarioId, int $filmeId, ?string $dataLocacao, ?string $usuarioNome, ?string $filmeTitulo){
        $this->id = $id;
        $this->usuarioId = $usuarioId;
        $this->filmeId = $filmeId;
        $this->dataLocacao = $dataLocacao;
        $this->usuarioNome = $usuarioNome;
        $this->filmeTitulo = $filmeTitulo;
    }

    //Factory com validação: usuário e filme precisam ser identificadores válidos.
    public static function criar(?int $id, ?int $usuarioId, ?int $filmeId, ?string $dataLocacao = null, ?string $usuarioNome = null, ?string $filmeTitulo = null): static {
        if ($usuarioId === null || $usuarioId <= 0) {
            throw new \InvalidArgumentException("Usuário inválido para a locação");
        }
        if ($filmeId === null || $filmeId <= 0) {
            throw new \InvalidArgumentException("Filme inválido para a locação");
        }
        return new static($id, $usuarioId, $filmeId, $dataLocacao, $usuarioNome, $filmeTitulo);
    }

    public function getId(): ?int { return $this->id; }
    public function getUsuarioId(): int { return $this->usuarioId; }
    public function getFilmeId(): int { return $this->filmeId; }
    public function getDataLocacao(): ?string { return $this->dataLocacao; }
    public function getUsuarioNome(): ?string { return $this->usuarioNome; }
    public function getFilmeTitulo(): ?string { return $this->filmeTitulo; }
}
