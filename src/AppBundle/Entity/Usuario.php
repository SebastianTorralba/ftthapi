<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table("dbo.usuarios")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UsuarioRepository")
 */
class Usuario implements UserInterface
{
    /**
     * @ORM\Column(name="id_usuario", type="decimal", precision=18, scale=0)
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(name="dni", type="string", length=10)
     * @Assert\NotBlank(message="Por favor, ingrese dni", groups={"request"})
     * @Assert\Length(
     *      min = 7,
     *      max = 10,
     *      minMessage = "Debe contener al menos {{ limit }} caracteres",
     *      maxMessage = "No puede contener más de {{ limit }} caracteres",
     *      groups={"request"}
     * )

     */
    private $dni;

    /**
     * @ORM\Column(name="id_hash", type="string", length=255)
     */
    private $hash;

    /**
     * @ORM\Column(name="desc_usr", type="string", length=100)
     */
    private $nombre;

    /**
     * @ORM\Column(name="email", type="string", length=100)
     * @Assert\NotBlank(message="Por favor, ingrese email", groups={"request"})
     * @Assert\Email(message="Por favor, ingrese un email valido", groups={"request"})
     */
    private $email;

    /**
     * @ORM\Column(name="nom_usr", type="string", length=100)
     * @Assert\NotBlank(message="Por favor, ingrese nombre de usuario", groups={"reset"})
     * @Assert\Length(
     *      min = 5,
     *      max = 10,
     *      minMessage = "Debe contener al menos {{ limit }} caracteres",
     *      maxMessage = "No puede contener más de {{ limit }} caracteres",
     *      groups={"reset"}
     * )
     */
    private $username;

    /**
     * @ORM\Column(name="password2016", type="string", length=100)
     * @Assert\NotBlank(message="Por favor, ingrese clave",groups={"reset"})
     * @Assert\Length(
     *      min = 5,
     *      max = 8,
     *      minMessage = "Debe contener al menos {{ limit }} caracteres",
     *      maxMessage = "No puede contener más de {{ limit }} caracteres",
     *      groups={"reset"}
     * )
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=true,
     *     message="Debe contener al menos un número",
     *     groups={"reset"}
     * )
     * @Assert\Regex(
     *     pattern="/[A-Z]/",
     *     match=true,
     *     message="Debe contener al menos una letra mayúscula",
     *     groups={"reset"}
     * )
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario\Perfil", cascade={"persist"})
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id_usuario")
     */
    private $perfil;


    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return utf8_encode($this->nombre);
    }

    public function getUsername()
    {
        return utf8_encode($this->username);
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        $perfil = array("ROLE_USER");
        $perfil[] = 'ROLE_'.str_replace(" ", "_", strtoupper($this->getPerfil()->getNombre()));

        if ($this->getPerfil()->getNombre() == "COMUNICACION") {
                $perfil[] = "ROLE_MULTIMEDIA";
        }

	if ($this->getPerfil()->getNombre() == "DESARROLLADOR") {
		$perfil[] = "ROLE_COMUNICACION";
		$perfil[] = "ROLE_MULTIMEDIA";
	}



        return $perfil;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->hash,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->hash,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Usuario
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Usuario
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Usuario
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Usuario
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set perfil
     *
     * @param \AppBundle\Entity\Usuario\Perfil $perfil
     *
     * @return Usuario
     */
    public function setPerfil(\AppBundle\Entity\Usuario\Perfil $perfil = null)
    {
        $this->perfil = $perfil;

        return $this;
    }

    /**
     * Get perfil
     *
     * @return \AppBundle\Entity\Usuario\Perfil
     */
    public function getPerfil()
    {
        return $this->perfil;
    }

    /**
     * Set dni
     *
     * @param string $dni
     *
     * @return Usuario
     */
    public function setDni($dni)
    {
        $this->dni = $dni;

        return $this;
    }

    /**
     * Get dni
     *
     * @return string
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * Set hash
     *
     * @param string $hash
     *
     * @return Usuario
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Usuario
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
}
