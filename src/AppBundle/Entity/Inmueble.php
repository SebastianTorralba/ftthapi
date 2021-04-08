<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("dbo.inmuebles")
 * @ORM\Entity()
 */
class Inmueble
{
  const ESTADO_PENDIENTE    = 1;
  const ESTADO_BAJADA_LISTA = 2;
  const ESTADO_CONECTADO    = 3;
  const ESTADO_CORTADO      = 5;
  const ESTADO_SUSPENDIDO   = 6;
  const ESTADO_SUSPENDIDO_GESTION_DE_CORTE_1 = 61;
  const ESTADO_SUSPENDIDO_GESTION_DE_CORTE_2 = 62;
  const ESTADO_ZONA_NO_CACLEADO = 9;

  public static $estados = array(
      self::ESTADO_PENDIENTE    => 'Pendiente',
      self::ESTADO_CONECTADO    => 'Conectado',
      self::ESTADO_SUSPENDIDO   => 'Suspendido',
      self::ESTADO_BAJADA_LISTA => 'Bajada lista',
      self::ESTADO_CORTADO      => 'Cortado',
      self::ESTADO_SUSPENDIDO_GESTION_DE_CORTE_1  => 'Gestión de Corte(3 facturas moroso)',
      self::ESTADO_SUSPENDIDO_GESTION_DE_CORTE_2  => 'Gestión de Corte',
      self::ESTADO_ZONA_NO_CACLEADO => "Zona no cableada",
  );


  /**
   * @ORM\Column(name="id_inmueble", type="decimal", precision=18, scale=0)
   * @ORM\Id
   */
  private $id;

  /**
   * @ORM\Column(name="dom_numero", type="string", length=10)
   */
  private $numero;

  /**
   * @ORM\Column(name="dom_manzana", type="string", length=100)
   */
  private $manzana;

  /**
   * @ORM\ManyToOne(targetEntity="Barrio", cascade={"persist"})
   * @ORM\JoinColumn(name="cod_barrio", referencedColumnName="cod_barrio")
   */
  private $barrio;

  /**
   * @ORM\ManyToOne(targetEntity="Calle", cascade={"persist"})
   * @ORM\JoinColumn(name="cod_calle", referencedColumnName="cod_calle")
   */
  private $calle;

  /**
   * @ORM\ManyToOne(targetEntity="Localidad", cascade={"persist"})
   * @ORM\JoinColumn(name="id_localidad", referencedColumnName="ccodloca")
   */
  private $localidad;

  /**
   * @ORM\Column(name="dom_piso", type="string", length=10)
   */
  private $piso;

  /**
   * @ORM\Column(name="Estado_Servicio", type="integer")
   */
  private $estado;

  /**
   * @ORM\Column(name="latitud", type="string", length=30)
   */
  private $latitud;

  /**
   * @ORM\Column(name="longitud", type="string", length=30)
   */
  private $longitud;

  public function isConectado()
  {
     return $this->estado == self::ESTADO_CONECTADO ? true : false  ;
  }

  public function isPendiente()
  {
     return $this->estado == self::ESTADO_PENDIENTE || $this->estado == self::ESTADO_BAJADA_LISTA ? true : false  ;
  }

  public function isSinServicio()
  {
     return $this->estado == self::ESTADO_CORTADO || $this->estado == self::ESTADO_SUSPENDIDO ? true : false  ;
  }

  public function inEstadoDeclarados()
  {
      return array_key_exists($this->estado, self::$estados);
  }



  /**
   * Set id
   *
   * @param string $id
   *
   * @return Inmueble
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
   * Set numero
   *
   * @param string $numero
   *
   * @return Inmueble
   */
  public function setNumero($numero)
  {
      $this->numero = $numero;

      return $this;
  }

  /**
   * Get numero
   *
   * @return string
   */
  public function getNumero()
  {
      return $this->numero;
  }

  /**
   * Set manzana
   *
   * @param string $manzana
   *
   * @return Inmueble
   */
  public function setManzana($manzana)
  {
      $this->manzana = $manzana;

      return $this;
  }

  /**
   * Get manzana
   *
   * @return string
   */
  public function getManzana()
  {
      return $this->manzana;
  }

  /**
   * Set piso
   *
   * @param string $piso
   *
   * @return Inmueble
   */
  public function setPiso($piso)
  {
      $this->piso = $piso;

      return $this;
  }

  /**
   * Get piso
   *
   * @return string
   */
  public function getPiso()
  {
      return $this->piso;
  }

  /**
   * Set estado
   *
   * @param integer $estado
   *
   * @return Inmueble
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

  /**
   * Set latitud
   *
   * @param string $latitud
   *
   * @return Inmueble
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
      return (float)$this->latitud;
  }

  /**
   * Set longitud
   *
   * @param string $longitud
   *
   * @return Inmueble
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
      return (float)$this->longitud;
  }

  /**
   * Set barrio
   *
   * @param \AppBundle\Entity\Barrio $barrio
   *
   * @return Inmueble
   */
  public function setBarrio(\AppBundle\Entity\Barrio $barrio = null)
  {
      $this->barrio = $barrio;

      return $this;
  }

  /**
   * Get barrio
   *
   * @return \AppBundle\Entity\Barrio
   */
  public function getBarrio()
  {
      return $this->barrio;
  }

  /**
   * Set calle
   *
   * @param \AppBundle\Entity\Calle $calle
   *
   * @return Inmueble
   */
  public function setCalle(\AppBundle\Entity\Calle $calle = null)
  {
      $this->calle = $calle;

      return $this;
  }

  /**
   * Get calle
   *
   * @return \AppBundle\Entity\Calle
   */
  public function getCalle()
  {
      return $this->calle;
  }

  /**
   * Set localidad
   *
   * @param \AppBundle\Entity\Localidad $localidad
   *
   * @return Inmueble
   */
  public function setLocalidad(\AppBundle\Entity\Localidad $localidad = null)
  {
      $this->localidad = $localidad;

      return $this;
  }

  /**
   * Get localidad
   *
   * @return \AppBundle\Entity\Localidad
   */
  public function getLocalidad()
  {
      return $this->localidad;
  }
}
