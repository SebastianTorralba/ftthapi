<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("dbo.localidades")
 * @ORM\Entity()
 */
class Localidad
{
    /**
     * @ORM\Column(name="ccodloca", type="decimal", precision=18, scale=0)
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(name="cloca", type="string", length=100)
     */
    private $nombre;

    /**
     * @ORM\Column(name="ccodpos", type="string", length=100)
     */
    private $cp;

    /**
     * Set id
     *
     * @param string $id
     * @return Barrio
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
     * @return Barrio
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
        return $this->getNombre();
    }

    /**
     * Set cp
     *
     * @param string $cp
     * @return Localidad
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * Get cp
     *
     * @return string
     */
    public function getCp()
    {
        return $this->cp;
    }
}
