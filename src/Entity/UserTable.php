<?php

namespace App\Entity;

use App\Repository\UserTableRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=UserTableRepository::class)
 */
class UserTable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $interest;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contact;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
//        blank field validation
        $metadata->addPropertyConstraint('name', new Assert\NotBlank([
            'message' => 'Name must not be null'
        ]));
        $metadata->addPropertyConstraint('password', new Assert\NotBlank([
            'message' => 'Password must not be null'
        ]));
        $metadata->addPropertyConstraint('email', new Assert\NotBlank([
            'message' => 'Email must not be null'
        ]));
        $metadata->addPropertyConstraint('interest', new Assert\NotBlank([
            'message' => 'Genre must not be null'
        ]));
        $metadata->addPropertyConstraint('contact', new Assert\NotBlank([
            'message' => 'Contact must not be null'
        ]));
//        other extra validation
        $metadata->addPropertyConstraint('email', new Assert\Email([
            'message' => 'The email "{{ value }}" is not a valid email.',
        ]));
        $metadata->addPropertyConstraint('contact', new Assert\Length(array(
            'min' => 1,
            'max' => 10,
            'minMessage' => 'phone number must be  greater than 0 digit',
            'maxMessage' => 'phone number must be  less than 10 digit'

        )));

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getInterest(): ?string
    {
        return $this->interest;
    }

    public function setInterest(?string $interest): self
    {
        $this->interest = $interest;

        return $this;
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

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }
}
