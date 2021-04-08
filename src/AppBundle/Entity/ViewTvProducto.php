<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("dbo.view_tv_producto")
 * @ORM\Entity()
 */
class ViewTvProducto
{
    /**
     * @ORM\Column(name="producto_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Por favor, ingrese nombre de su amigo.")
     * @ORM\Column(name="producto_descip", type="string", length=200)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ViewTvProducto\Canal", mappedBy="producto")
     */
    private $canales;

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
     * @return ViewTvProducto
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
     * Constructor
     */
    public function __construct()
    {
        $this->canales = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add canale
     *
     * @param \AppBundle\Entity\ViewTvProducto\Canal $canale
     *
     * @return ViewTvProducto
     */
    public function addCanale(\AppBundle\Entity\ViewTvProducto\Canal $canale)
    {
        $this->canales[] = $canale;

        return $this;
    }

    /**
     * Remove canale
     *
     * @param \AppBundle\Entity\ViewTvProducto\Canal $canale
     */
    public function removeCanale(\AppBundle\Entity\ViewTvProducto\Canal $canale)
    {
        $this->canales->removeElement($canale);
    }

    /**
     * Get canales
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCanales()
    {
        return $this->canales;
    }
}
