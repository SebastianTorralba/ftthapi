<?php

namespace AppBundle\Entity\Red\Topologia;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("dbo.red_elemento_georeferencia")
 * @ORM\Entity()
 */
class ElementoGeoreferencia
{
    /**
     * @ORM\Column(name="id", type="decimal", precision=18, scale=0)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="latitud", type="string", length=255)
     */
    private $latitud;

    /**
     * @ORM\Column(name="longitud", type="string", length=255)
     */
    private $longitud;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Red\Elemento", inversedBy="georeferencias")
     * @ORM\JoinColumn(name="elemento_id", referencedColumnName="id")
     */
    private $elemento;

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
     * Set latitud
     *
     * @param string $latitud
     *
     * @return ElementoGeoreferencia
     */
    public function setLatitud($latitud)
    {
        $this->latitud = $latitud;

        return $this;
    }

    /**
     * Get latitud
     *
     * @return string
     */
    public function getLatitud()
    {
        return $this->latitud;
    }

    /**
     * Set longitud
     *
     * @param string $longitud
     *
     * @return ElementoGeoreferencia
     */
    public function setLongitud($longitud)
    {
        $this->longitud = $longitud;

        return $this;
    }

    /**
     * Get longitud
     *
     * @return string
     */
    public function getLongitud()
    {
        return $this->longitud;
    }

    /**
     * Set elemento
     *
     * @param \AppBundle\Entity\Red\Topologia\Elemento $elemento
     *
     * @return ElementoGeoreferencia
     */
    public function setElemento(\AppBundle\Entity\Red\Elemento $elemento = null)
    {
        $this->elemento = $elemento;

        return $this;
    }

    /**
     * Get elemento
     *
     * @return \AppBundle\Entity\Red\Topologia\Elemento
     */
    public function getElemento()
    {
        return $this->elemento;
    }
}
