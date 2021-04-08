<?php

namespace AppBundle\Entity\Red\Topologia;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("dbo.red_elemento_atributo")
 * @ORM\Entity()
 */
class ElementoAtributo
{
    /**
     * @ORM\Column(name="id", type="decimal", precision=18, scale=0)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="valor", type="string", length=255)
     */
    private $valor;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Red\Elemento", inversedBy="atributos")
     * @ORM\JoinColumn(name="elemento_id", referencedColumnName="id")
     */
    private $elemento;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Red\Topologia\ElementoTipoAtributo")
     * @ORM\JoinColumn(name="elemento_tipo_atributo_id", referencedColumnName="id")
     */
    private $elementoTipoAtributo;

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
     * Set valor
     *
     * @param string $valor
     *
     * @return ElementoAtributo
     */
    public function setValor($valor)
    {
        $this->valor = utf8_decode($valor);

        return $this;
    }

    /**
     * Get valor
     *
     * @return string
     */
    public function getValor()
    {
        return utf8_encode($this->valor);
    }

    /**
     * Set elemento
     *
     * @param \AppBundle\Entity\Red\Elemento $elemento
     *
     * @return ElementoAtributo
     */
    public function setElemento(\AppBundle\Entity\Red\Elemento $elemento = null)
    {
        $this->elemento = $elemento;

        return $this;
    }

    public function getElemento()
    {
        return $this->elemento;
    }

    public function setElementoTipoAtributo(\AppBundle\Entity\Red\Topologia\ElementoTipoAtributo $elementoTipoAtributo = null)
    {
        $this->elementoTipoAtributo = $elementoTipoAtributo;

        return $this;
    }

    /**
     * Get elementoTipoAtributo
     *
     * @return \AppBundle\Entity\Red\Topologia\ElementoTipoAtributo
     */
    public function getElementoTipoAtributo()
    {
        return $this->elementoTipoAtributo;
    }
}
