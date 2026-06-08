<?php

namespace League\FactoryMuffin\Test;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(length: 140)]
    private $name;

    #[ORM\Column(length: 140)]
    private $email;

    #[ORM\OneToMany(targetEntity: Cat::class, mappedBy: 'user', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private $cats;

    public function __construct()
    {
        $this->cats = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getCats()
    {
        return $this->cats;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
