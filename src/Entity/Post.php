<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 *
 * @ORM\Table(name="post", uniqueConstraints={@ORM\UniqueConstraint(name="id_user", columns={"id_user", "id_acteur"})})
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_post", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPost;

    /**
     * @var int
     *
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     */
    private $idUser;

    /**
     * @var int
     *
     * @ORM\Column(name="id_acteur", type="integer", nullable=false)
     */
    private $idActeur;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $dateAdd;//'current_timestamp()';

    /**
     * @var string
     *
     * @ORM\Column(name="post", type="text", length=0, nullable=false)
     */
    private $post;

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

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(\DateTimeInterface $dateAdd): self
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    public function getPost(): ?string
    {
        return $this->post;
    }

    public function setPost(string $post): self
    {
        $this->post = $post;

        return $this;
    }


}
