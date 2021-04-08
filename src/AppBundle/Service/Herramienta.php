<?php

namespace AppBundle\Service;

use \Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity as Entity;

class Herramienta
{
    private $container;
    private $em;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    public function validarUsuario($usuario)
    {

        if ($usuario) {

            if (!$usuario instanceof Entity\Usuario) {
                $usuario = $this->em->getRepository(Entity\Usuario::class)->findOneBy(["hash" => $usuario]);
            }

            if ($usuario) {
                return $usuario;
            }
        }

        return null;
    }

}
