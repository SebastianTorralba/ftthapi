<?php

namespace AppBundle\Controller\Sistema\Red;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use AppBundle\Entity as Entity;
use AppBundle\Form as Form;
use AppBundle\Entity\Red\Topologia\ElementoGeoreferencia;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/{usuario_hash}/red/disenio")
*/
class DisenioController extends Controller
{
    /**
     * @Route("/", name="red_disenio")
     */
    public function indexAction(Request $request, $usuario_hash = "")
    {
        $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
        if(!$usuario) {
            throw $this->createNotFoundException();
        }

        return $this->render('sistema/red/disenio.html.twig', [
          "usuario_hash" => $usuario_hash
        ]);
    }

    /**
     * @Route("/get-tipos-elementos", name="red_disenio_get_tipos_elementos")
     */
    public function getTiposElementosAction(Request $request, $usuario_hash = "")
    {
        $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
        if(!$usuario) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $response = array();

/*        if(! $this->get('app.herramienta')->validarUsuario($usuario)) {
            throw $this->createNotFoundException();
        }*/

        $where = '';
        $response = array(
            "tiposElementos" => []
        );

        $tiposElementos = $em->getRepository('AppBundle:Red\Topologia\ElementoTipo')->findBy([], ["nombre" => "ASC"]);

        $i = 0;
        foreach($tiposElementos as $tipoElemento) {

            $response["tiposElementos"][$i]["id"] = $tipoElemento->getId();
            $response["tiposElementos"][$i]["value"] = $tipoElemento->getId();
            $response["tiposElementos"][$i]["nombre"] = $tipoElemento->getNombre();
            $response["tiposElementos"][$i]["label"] = $tipoElemento->getNombre();
            $response["tiposElementos"][$i]["tipoGeoreferencia"] = $tipoElemento->getTipoGeoreferencia();
            $response["tiposElementos"][$i]["color"] = $tipoElemento->getColorHexa();
            $response["tiposElementos"][$i]["svgPath"] = $tipoElemento->getSvgPath();

            $i++;
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/get-tipo-elemento-atributo/{elementoTipoId}", name="red_disenio_get_tipo_elemento_atributo")
     */
    public function getTipoElementoAtributoAction($elementoTipoId = 0, Request $request, $usuario_hash = "")
    {
        $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
        if(!$usuario) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $response = array();

/*        if(! $this->get('app.herramienta')->validarUsuario($usuario)) {
            throw $this->createNotFoundException();
        }*/

        $where = '';
        $response = array(
            "atributos" => []
        );

        $tipoElemento = $em->getRepository('AppBundle:Red\Topologia\ElementoTipo')->find($elementoTipoId);

        if ($tipoElemento) {

          $i = 0;
          foreach($tipoElemento->getAtributos() as $atributo) {

              $response["atributos"][$i]["id"]     = $atributo->getId();
              $response["atributos"][$i]["nombre"] = $atributo->getNombre();
              //$response["atributos"][$i]["value"]  = $atributo->getValor();

              $i++;
          }
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/elemento/save", name="red_disenio_elemento_save")
     */
    public function elementoSaveAction(Request $request, $usuario_hash = "")
    {
        $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
        if(!$usuario) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $elementoRequest = json_decode($request->getContent(), true);
        $response = [
          "resultado" => "no",
        ];


        $elemento = null;
        if($elementoRequest && isset($elementoRequest["elemento"]) && isset($elementoRequest["elemento"]["id"])) {

          $elemento = $em->getRepository("AppBundle:Red\Elemento")->find($elementoRequest["elemento"]["id"]);
          if (!$elemento) {
            $elemento = new Entity\Red\Elemento();
            $elemento->getEstadoActual()->setCreadaPor($usuario);
          }

          if ($elementoRequest["elemento"]["elementoPadre"] == 0) {
            $elemento->setParent(NULL);
          }else {

            $elementoPadre = $em->getRepository("AppBundle:Red\Elemento")->find($elementoRequest["elemento"]["elementoPadre"]);
            if ($elementoPadre) {
              $elemento->setParent($elementoPadre);
            }
          }

          $elemento->setNombre($elementoRequest["elemento"]["nombre"]);

          $elementoTipo = $em->getRepository('AppBundle:Red\Topologia\ElementoTipo')->find($elementoRequest["elemento"]["elementoTipo"]);
          if ($elementoTipo) {

            $elemento->setElementoTipo($elementoTipo);

            // gestiono los atributos
            foreach ($elemento->getAtributos() as $atributo) {
              $em->remove($atributo);
              $em->flush();
            }

            foreach ($elementoRequest["elemento"]["atributos"] as $atributoRequest) {

              $elementoTipoAtributo = $em->getRepository('AppBundle:Red\Topologia\ElementoTipoAtributo')->find($atributoRequest[0]);
              if ($elementoTipoAtributo) {
                $atributo = new Entity\Red\Topologia\ElementoAtributo();
                $atributo->setElementoTipoAtributo($elementoTipoAtributo);
                $atributo->setValor($atributoRequest[1]);
                $elemento->addAtributo($atributo);
              }

            }

            // gestiono las georeferencias
            foreach ($elemento->getGeoreferencias() as $georeferencia) {
              $em->remove($georeferencia);
              $em->flush();
            }

            foreach ($elementoRequest["elemento"]["georeferencias"] as $georeferenciaRequest) {
              $georeferencia = new Entity\Red\Topologia\ElementoGeoreferencia();
              $georeferencia->setLatitud((string)$georeferenciaRequest[0]);
              $georeferencia->setLongitud((string)$georeferenciaRequest[1]);
              $elemento->addGeoreferencia($georeferencia);
            }

          }

          $em->persist($elemento);
          $em->flush();

          $response = [
            "resultado" => "ok",
            "elemento" => $elemento->getElementoArray()
          ];
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/elementos", name="red_disenio_elementos")
     */
    public function listadoAction(Request $request, $usuario_hash = "")
    {
        $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
        if(!$usuario) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $response = array();

        $response = array(
            "elementos" => []
        );

        $searchString = $request->query->has("s") ? $request->query->get("s") : "";
        $searchTipo   = $request->query->has("t") ? $request->query->get("t") : 0;
        $searchCodigo = $request->query->has("c") ? $request->query->get("c") : 0;
        $searchNombre = $request->query->has("n") ? $request->query->get("n") : 0;

        $response["elementos"] = $em->getRepository('AppBundle:Red\Elemento')->findByParams($searchString, $searchTipo, $searchCodigo, $searchNombre);

        return new JsonResponse($response);
    }


    /**
     * @Route("/elemento/{id}", name="red_disenio_elemento")
     */
    public function elementoAction($id = 0, $usuario_hash = "")
    {
        $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
        if(!$usuario) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $response = array();

        $response = array(
            "elemento" => []
        );

        $elemento = $em->getRepository('AppBundle:Red\Elemento')->find($id);
        if ($elemento) {
          $response["elemento"] = $elemento->getElementoArray();
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/eliminar/elemento", name="red_disenio_elemento_eliminar")
     */
    public function elementoEliminarAction(Request $request, $usuario_hash = "")
    {
      $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
      if(!$usuario) {
          throw $this->createNotFoundException();
      }

      $em = $this->getDoctrine()->getManager();

      $elementoRequest = json_decode($request->getContent(), true);
      $elemento = $em->getRepository("AppBundle:Red\Elemento")->find($elementoRequest["id"]);
      $response = ["resultado" => "no ".$elementoRequest["id"]];

      if ($elemento) {

         // como voy a eliminar el elemento, le asigno su padre como padre a sus hijos
          foreach($elemento->getChildren() as $child){
            $child->setParent($elemento->getParent());
            $em->flush();
          }

          // para que se actualice los child asociados al objeto
          $elemento = $em->getRepository("AppBundle:Red\Elemento")->find($elementoRequest["id"]);

          // elimino el elemento
          $em->remove($elemento);
          $em->flush();

          $response = ["resultado" => "ok"];
      }

      return new JsonResponse($response);
    }
}
