<?php

namespace AppBundle\Form\Model;
use Symfony\Component\Validator\Constraints as Assert;

class Recomendar 
{
    /**
     * @Assert\NotBlank(message="Por favor, ingrese email.")
     * @Assert\Email(message="El email ingresado no es válido")
    */
    private $email;
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function setEmail($email)
    {
        $this->email = $email;
    }
}