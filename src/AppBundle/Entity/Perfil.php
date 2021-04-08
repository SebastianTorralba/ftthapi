<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("dbo.perfiles")
 * @ORM\Entity()
 */
class Perfil
{
    /**
     * @ORM\Column(name="nom_perfil", type="string", length=200)
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(name="desc_perfil", type="string", length=200)
     */
    private $nombre;

    /**
     * @ORM\Column(name="app_celular_version", type="string", length=200)
     */
    private $appVersion;

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Perfil
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
     * @return Perfil
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
     * Set appVersion
     *
     * @param string $appVersion
     *
     * @return Perfil
     */
    public function setAppVersion($appVersion)
    {
        $this->appVersion = $appVersion;

        return $this;
    }

    /**
     * Get appVersion
     *
     * @return string
     */
    public function getAppVersion()
    {
        return $this->appVersion;
    }
}
