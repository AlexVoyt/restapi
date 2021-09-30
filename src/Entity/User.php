<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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

    public function __construct()
    {
        $this->todoThingies = new ArrayCollection();
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
}
