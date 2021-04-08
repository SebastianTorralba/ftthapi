<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("dbo.departamentos")
 * @ORM\Entity()
 */
class Departamento
{
    /**
     * @ORM\Column(name="id_dpto", type="decimal", precision=18, scale=0)
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(name="cdpto", type="string", length=100)
     */
    private $nombre;

    /**
     * Set id
     *
     * @param string $id
     * @return Departamento
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
     * @return Departamento
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
        return utf8_encode($this->nombre);
    }
}
