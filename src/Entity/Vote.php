<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vote
 *
 * @ORM\Table(name="vote", uniqueConstraints={@ORM\UniqueConstraint(name="id_user", columns={"id_user", "id_acteur"})})
 * @ORM\Entity(repositoryClass="App\Repository\VoteRepository")
 */
class Vote
{
    /**
     * @var int
     * @ORM\Id 
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     */
    private $idUser;

    /**
     * @var int
     * @ORM\Id 
     * @ORM\Column(name="id_acteur", type="integer", nullable=false)
     */
    private $idActeur;

    /**
     * @var int
     *
     * @ORM\Column(name="vote", type="integer", nullable=false)
     */
    private $vote;

    public function getIdPost(): ?int
    {
        return $this->idPost;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIdActeur(): ?int
    {
        return $this->idActeur;
    }

    public function setIdActeur(int $idActeur): self
    {
        $this->idActeur = $idActeur;

        return $this;
    }

    public function getVote(): ?int
    {
        return $this->vote;
    }

    public function setVote(int $vote): self
    {
        $this->vote = $vote;

        return $this;
    }

}
