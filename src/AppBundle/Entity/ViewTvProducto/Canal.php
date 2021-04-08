<?php

namespace AppBundle\Entity\ViewTvProducto;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("dbo.view_tv_productos_canales")
 * @ORM\Entity()
 */
class Canal
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
     * @ORM\Column(name="numero", type="integer", length=200) 
     */
    private $numero;    
    
    /**
     * @ORM\Column(name="imagen", type="integer", length=200) 
     */
    private $imagen;       
    
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ViewTvProducto\Canal\CanalCategoria", mappedBy="canal")
     */
    private $categorias;        
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ViewTvProducto", inversedBy="canales")
     */
    private $producto;            
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categorias = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Canal
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
     * Set numero
     *
     * @param integer $numero
     *
     * @return Canal
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set imagen
     *
     * @param integer $imagen
     *
     * @return Canal
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;

        return $this;
    }

    /**
     * Get imagen
     *
     * @return integer
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * Add categoria
     *
     * @param \AppBundle\Entity\ViewTvProducto\Canal\CanalCategoria $categoria
     *
     * @return Canal
     */
    public function addCategoria(\AppBundle\Entity\ViewTvProducto\Canal\CanalCategoria $categoria)
    {
        $this->categorias[] = $categoria;

        return $this;
    }

    /**
     * Remove categoria
     *
     * @param \AppBundle\Entity\ViewTvProducto\Canal\CanalCategoria $categoria
     */
    public function removeCategoria(\AppBundle\Entity\ViewTvProducto\Canal\CanalCategoria $categoria)
    {
        $this->categorias->removeElement($categoria);
    }

    /**
     * Get categorias
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategorias()
    {
        return $this->categorias;
    }

    /**
     * Set producto
     *
     * @param \AppBundle\Entity\ViewTvProducto $producto
     *
     * @return Canal
     */
    public function setProducto(\AppBundle\Entity\ViewTvProducto $producto = null)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto
     *
     * @return \AppBundle\Entity\ViewTvProducto
     */
    public function getProducto()
    {
        return $this->producto;
    }
}
