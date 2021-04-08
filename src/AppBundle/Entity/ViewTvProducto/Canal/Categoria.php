<?php

namespace AppBundle\Entity\ViewTvProducto\Canal;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("dbo.view_tv_productos_canales_categorias")
 * @ORM\Entity()
 */
class Categoria
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(name="nombre", type="string", length=200) 
     */
    private $nombre;
    
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ViewTvProducto\Canal\CanalCategoria", mappedBy="categoria")
     */
    private $canales;        
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->canales = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Categoria
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

    /**
     * Add canale
     *
     * @param \AppBundle\Entity\ViewTvProducto\Canal\CanalCategoria $canale
     *
     * @return Categoria
     */
    public function addCanale(\AppBundle\Entity\ViewTvProducto\Canal\CanalCategoria $canale)
    {
        $this->canales[] = $canale;

        return $this;
    }

    /**
     * Remove canale
     *
     * @param \AppBundle\Entity\ViewTvProducto\Canal\CanalCategoria $canale
     */
    public function removeCanale(\AppBundle\Entity\ViewTvProducto\Canal\CanalCategoria $canale)
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
