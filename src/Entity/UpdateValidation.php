<?php

namespace App\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;



class UpdateValidation
{
    private $email;
    private $contact;
    private $interest;

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
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param mixed $contact
     */
    public function setContact($contact): void
    {
        $this->contact = $contact;
    }

    /**
     * @return mixed
     */
    public function getInterest()
    {
        return $this->interest;
    }

    /**
     * @param mixed $interest
     */
    public function setInterest($interest): void
    {
        $this->interest = $interest;
    }


    /**
     * This function is called when doing form data validation.
     * 
     * @param ClassMetadata $metadata
     * Contains information of the form data.
     * 
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Empty field check validation
        $metadata->addPropertyConstraint('email', new Assert\NotBlank([
            'message' => 'Email must not be null'
        ]));
        $metadata->addPropertyConstraint('interest', new Assert\NotBlank([
            'message' => 'Genre must not be null'
        ]));
        $metadata->addPropertyConstraint('contact', new Assert\NotBlank([
            'message' => 'Contact must not be null'
        ]));

        // Other extra validation for email and contact.
        $metadata->addPropertyConstraint('email', new Assert\Email([
            'message' => 'The email "{{ value }}" is not a valid email.',
        ]));
        $metadata->addPropertyConstraint('contact', new Assert\Length(array(
            'min' => 10,
            'max' => 10,
            'minMessage' => 'phone number must be  equal to 10 digit',
            'maxMessage' => 'phone number must be  less than 10 digit'

        )));
    }
}