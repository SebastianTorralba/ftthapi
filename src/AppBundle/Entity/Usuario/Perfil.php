<?php

namespace AppBundle\Entity\Usuario;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("dbo.usuario_perfil")
 * @ORM\Entity()
 */
class Perfil
{
    /**
     * @ORM\Column(name="id_usuario", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(name="nom_perfil", type="string", length=255)
     */
    private $nombre;      

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Perfil
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Perfil
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }
}
