<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("dbo.cuadrillas")
 * @ORM\Entity()
 */
class Cuadrilla
{
    /**
     * @ORM\Column(name="id_cuadrilla", type="decimal", precision=18, scale=0)
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(name="descripcion", type="string", length=100)
     */
    private $nombre;

    /**
     * @ORM\Column(name="estado", type="integer")
     */
    private $estado;

    public function getArray()
    {
      $entity = [];

      $entity["id"] = $this->getId();
      $entity["value"] = $this->getId();

      $entity["nombre"] = $this->getNombre();
      $entity["label"] = $this->getNombre();

      return $entity;
    }


    /**
     * Set id
     *
     * @param string $id
     *
     * @return Cuadrilla
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
     *
     * @return Cuadrilla
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

    /**
     * Set estado
     *
     * @param integer $estado
     *
     * @return Cuadrilla
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return integer
     */
    public function getEstado()
    {
        return $this->estado;
    }
}
