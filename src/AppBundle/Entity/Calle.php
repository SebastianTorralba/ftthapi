<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table("dbo.Calles")
 * @ORM\Entity()
 */
class Calle
{
    /**
     * @ORM\Column(name="cod_calle", type="decimal", precision=18, scale=0)
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(name="nom_calle", type="string", length=100)
     */
    private $nombre;

    /**
     * Set id
     *
     * @param string $id
     * @return Calle
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Calle
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
        return strtoupper(utf8_encode($this->nombre));
    }

    public function __toString() {
        return $this->getNombre();;
    }
}
