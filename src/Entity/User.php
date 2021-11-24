<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=TodoThingy::class, mappedBy="user", orphanRemoval=true)
     */
    private $todoThingies;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="owner", orphanRemoval=true)
     */
    private $files;

    public function __construct()
    {
        $this->todoThingies = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return Collection|TodoThingy[]
     */
    public function getTodoThingies(): Collection
    {
        return $this->todoThingies;
    }

    public function addTodoThingy(TodoThingy $todoThingy): self
    {
        if (!$this->todoThingies->contains($todoThingy)) {
            $this->todoThingies[] = $todoThingy;
            $todoThingy->setUser($this);
        }

        return $this;
    }

    public function removeTodoThingy(TodoThingy $todoThingy): self
    {
        if ($this->todoThingies->removeElement($todoThingy)) {
            // set the owning side to null (unless already changed)
            if ($todoThingy->getUser() === $this) {
                $todoThingy->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setOwner($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getOwner() === $this) {
                $file->setOwner(null);
            }
        }

        return $this;
    }
}
