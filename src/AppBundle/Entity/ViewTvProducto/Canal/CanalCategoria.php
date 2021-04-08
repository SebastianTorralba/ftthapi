<?php

namespace AppBundle\Entity\ViewTvProducto\Canal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("dbo.view_tv_productos_canales_canal_categoria")
 * @ORM\Entity()
 */
class CanalCategoria
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ViewTvProducto\Canal", inversedBy="categorias")
     * @ORM\JoinColumn(name="canal_id", referencedColumnName="id")
     */
    private $canal;
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ViewTvProducto\Canal\Categoria", inversedBy="canales")
     * @ORM\JoinColumn(name="categoria_id", referencedColumnName="id")
     */
    private $categoria;    
    
    /**
     * @ORM\Column(name="principal", type="boolean")
     */
    private $principal;        

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
     * Set principal
     *
     * @param boolean $principal
     *
     * @return CanalCategoria
     */
    public function setPrincipal($principal)
    {
        $this->principal = $principal;

        return $this;
    }

    /**
     * Get principal
     *
     * @return boolean
     */
    public function getPrincipal()
    {
        return $this->principal;
    }

    /**
     * Set canal
     *
     * @param \AppBundle\Entity\ViewTvProducto\Canal $canal
     *
     * @return CanalCategoria
     */
    public function setCanal(\AppBundle\Entity\ViewTvProducto\Canal $canal = null)
    {
        $this->canal = $canal;

        return $this;
    }

    /**
     * Get canal
     *
     * @return \AppBundle\Entity\ViewTvProducto\Canal
     */
    public function getCanal()
    {
        return $this->canal;
    }

    /**
     * Set categoria
     *
     * @param \AppBundle\Entity\ViewTvProducto\Canal\Categoria $categoria
     *
     * @return CanalCategoria
     */
    public function setCategoria(\AppBundle\Entity\ViewTvProducto\Canal\Categoria $categoria = null)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria
     *
     * @return \AppBundle\Entity\ViewTvProducto\Canal\Categoria
     */
    public function getCategoria()
    {
        return $this->categoria;
    }
}
