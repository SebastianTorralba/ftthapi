<?php

namespace AppBundle\Controller\Sistema\Utilidad;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity as Entity;
use AppBundle\Form as Form;

/**
 * @Route("/utilidad/googlemap")
*/
class GoogleMapSinSeguridadController extends Controller
{
    /**
     * @Route("/contrato/", name="contrato")
     */
    public function contratoAction(Request $request)
    {
        $parametros = "";

        if ($request->query->has("parametros")) {
            $parametros= $request->query->get("parametros");
        }

        return $this->render('sistema/utilidad/googlemap/contrato.html.twig', array(
            "parametros" => $parametros,
        ));
    }
    
    /**
     * @Route("/contratotest/", name="contratotest")
     */
    public function contratotestAction(Request $request)
    {
        $parametros = "";

        if ($request->query->has("parametros")) {
            $parametros= $request->query->get("parametros");
        }

        return $this->render('sistema/utilidad/googlemap/contratotest.html.twig', array(
            "parametros" => $parametros,
        ));
    }
}
