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


/**
 * @Route("/{usuario_hash}/red/gestion-obra")
*/
class GestionObraController extends Controller
{
    /**
     * @Route("/", name="red_gestionobra")
     */
    public function indexAction(Request $request, $usuario_hash = "")
    {
        $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
        if(!$usuario) {
            throw $this->createNotFoundException();
        }

        return $this->render('sistema/red/gestion-obra.html.twig', [
          "usuario_hash" => $usuario_hash
        ]);
    }

    /**
     * @Route("/obras", name="red_gestionobra_obras")
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
          "obras" => []
        );

        $searchString = $request->query->has("s") ? $request->query->get("s") : "";
        $searchElemento   = $request->query->has("e") ? $request->query->get("e") : 0;
        $searchNombre = $request->query->has("n") ? $request->query->get("n") : 0;

        $obras = $em->getRepository('AppBundle:Red\Obra')->findByParams($searchString, $searchElemento, $searchNombre);

        if ($obras) {

          $i = 0;
          foreach($obras as $obra) {

            $response["obras"][$i] = $obra->getArrayShort();
            $response["obras"][$i]["elementos"] = $em->getRepository('AppBundle:Red\Elemento')->findElementosObra($response["obras"][$i]["id"]);
            $i++;
          }
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/obra/save", name="red_gestionobra_obra_save")
     */
    public function obraSaveAction(Request $request, $usuario_hash = "")
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

        $obra = null;

        if($elementoRequest && isset($elementoRequest["obra"]) && isset($elementoRequest["obra"]["id"])) {

          $obra = $em->getRepository("AppBundle:Red\Obra")->find($elementoRequest["obra"]["id"]);
          if (!$obra) {
            $obra = new Entity\Red\Obra();
          }

          if(isset($elementoRequest["obra"]["nombre"])) $obra->setNombre($elementoRequest["obra"]["nombre"]);
          if(isset($elementoRequest["obra"]["fechaInicioEstimada"])) $obra->setFechaInicioEstimada(new \DateTime($elementoRequest["obra"]["fechaInicioEstimada"]));
          if(isset($elementoRequest["obra"]["fechaFinEstimada"])) $obra->setFechaFinEstimada(new \DateTime($elementoRequest["obra"]["fechaFinEstimada"]));


          if(isset($elementoRequest["obra"]["elementosNuevos"])) {
            foreach($elementoRequest["obra"]["elementosNuevos"] as $elementoNuevo){
              $elemento = $em->getRepository("AppBundle:Red\Elemento")->find($elementoNuevo);
              if ($elemento) {
                $en = new Entity\Red\Obra\Elemento();
                $en->setCreadaPor($usuario);
                $en->setElemento($elemento);
                $obra->addElemento($en);

                $estado = new Entity\Red\Elemento\Estado(Entity\Red\Elemento\Estado::ESTADO_EN_OBRA);
                $estado->setCreadaPor($usuario);
                $elemento->addEstado($estado);
              }
            }
          }

          if(isset($elementoRequest["obra"]["elementosQuitar"])) {
            foreach($elementoRequest["obra"]["elementosQuitar"] as $elementoQuitar){
              $elemento = $em->getRepository("AppBundle:Red\Obra\Elemento")->findOneByElemento($elementoQuitar);
              if ($elemento) {
                $estado = new Entity\Red\Elemento\Estado(Entity\Red\Elemento\Estado::ESTADO_PLANIFICADO);
                $estado->setCreadaPor($usuario);
                $elemento->getElemento()->addEstado($estado);
                $obra->removeElemento($elemento);
              }
            }
          }

        }

        $em->persist($obra);
        $em->flush();

        $response = [
          "resultado" => "ok",
          "obra" => $obra->getArray()
        ];

        return new JsonResponse($response);
    }

    /**
     * @Route("/obra/{id}", name="red_gestionobra_obra")
     */
    public function obraAction($id = 0, $usuario_hash = "")
    {
        $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
        if(!$usuario) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $response = array();

        $response = array(
            "obra" => []
        );

        $obra = $em->getRepository('AppBundle:Red\Obra')->find($id);
        if ($obra) {
          $response["obra"] = $obra->getArray();
          $response["obra"]["elementos"] = $em->getRepository('AppBundle:Red\Elemento')->findElementosObra($id);
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/elementos-red-sin-obra", name="red_gestionobra_elementosred_sin_obra")
     */
    public function elementosRedSinObraAction(Request $request, $usuario_hash = "")
    {
        $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
        if(!$usuario) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $response = array();

        $response = array(
          "elementosSinObra" => []
        );

        $elementos = $em->getRepository('AppBundle:Red\Elemento')->findElementosSinObra();

        if ($elementos) {
          $response["elementosSinObra"] = $elementos;
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/obra/{id}/tarea/{tareaId}", name="red_gestionobra_obra_tarea")
     */
    public function tareaAction($id = 0, $tareaId = 0, $usuario_hash = "")
    {
        $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
        if(!$usuario) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $response = array();

        $response = array(
            "tarea" => []
        );

        $tarea = $em->getRepository('AppBundle:Red\Obra\Tarea')->find($tareaId);
        if ($tarea) {
          $response["tarea"] = $tarea->getArray();
          $response["tarea"]["elementos"] = $em->getRepository('AppBundle:Red\Elemento')->findElementosTarea($tareaId);
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/obra/{id}/elementos-red-obra", name="red_gestionobra_elementosred_obra_sin_tarea")
     */
    public function elementosRedObraAction(Request $request, $id = 0, $tareaId = 0, $usuario_hash = "")
    {
        $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
        if(!$usuario) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();

        $response = array(
          "elementosObra" => []
        );

        $elementos = $em->getRepository('AppBundle:Red\Elemento')->findElementosObra($id);

        if ($elementos) {
          $response["elementosObra"] = $elementos;
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/cuadrillas-activas", name="red_cuadrillas_activas")
     */
    public function cuadrillasActivasAction(Request $request, $id = 0, $tareaId = 0, $usuario_hash = "")
    {
        $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
        if(!$usuario) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $response = array();

        $response = array(
          "cuadrillas" => []
        );

        $cuadrillas = $em->getRepository('AppBundle:Cuadrilla')->findBy(["estado" => 0]);

        $i = 0;
        foreach($cuadrillas as $cuadrilla) {
          $response["cuadrillas"][$i] = $cuadrilla->getArray();
          $i++;
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/obra/{id}/tarea/{tareaId}/save", name="red_gestionobra_obra__tarea_save")
     */
    public function tareaSaveAction(Request $request, $usuario_hash = "")
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

        $tarea = null;

        if($elementoRequest && isset($elementoRequest["tarea"]) && isset($elementoRequest["tarea"]["id"])) {

          $tarea = $em->getRepository("AppBundle:Red\Obra\Tarea")->find($elementoRequest["tarea"]["id"]);
          if (!$tarea) {
            $tarea = new Entity\Red\Obra\Tarea();
            $tarea->setCreadaPor($usuario);
          }

          $obra = $em->getRepository("AppBundle:Red\Obra")->find($elementoRequest["tarea"]["obra"]);
          if ($obra) {
            $tarea->setObra($obra);
          }

          $cuadrilla = $em->getRepository("AppBundle:Cuadrilla")->find($elementoRequest["tarea"]["cuadrilla"]);
          if ($cuadrilla) {
            $tarea->setCuadrilla($cuadrilla);
          }

          $tarea->setFecha(new \DateTime($elementoRequest["tarea"]["fecha"]));
          $tarea->setTipo($elementoRequest["tarea"]["tipo"]["value"]);
          $tarea->setObservacion($elementoRequest["tarea"]["observacion"]);

          if(isset($elementoRequest["tarea"]["elementosNuevos"])) {
            foreach($elementoRequest["tarea"]["elementosNuevos"] as $elementoNuevo){

              $elemento = $em->getRepository("AppBundle:Red\Obra\Elemento")->findOneBy(["elemento" => $elementoNuevo]);

              if ($elemento) {

                $tareaElemento = new Entity\Red\Obra\TareaElemento();
                $tareaElemento->setTarea($tarea);
                $tareaElemento->setElemento($elemento);
                $tareaElemento->setCreadaPor($usuario);
                $tarea->addElemento($tareaElemento);

                $estado = new Entity\Red\Elemento\Estado(Entity\Red\Elemento\Estado::ESTADO_EN_OBRA_CON_TAREA);
                $estado->setCreadaPor($usuario);

                $elemento->getElemento()->addEstado($estado);
              }
            }
          }

          if(isset($elementoRequest["tarea"]["elementosQuitar"])) {
            foreach($elementoRequest["tarea"]["elementosQuitar"] as $elementoQuitar){

              $tareaElemento = $em->getRepository("AppBundle:Red\Obra\TareaElemento")->find($elementoQuitar);

              if ($tareaElemento) {

                $estado = new Entity\Red\Elemento\Estado(Entity\Red\Elemento\Estado::ESTADO_EN_OBRA);
                $estado->setCreadaPor($usuario);

                $tareaElemento->getElemento()->getElemento()->addEstado($estado);
                $tarea->removeElemento($tareaElemento);
              }
            }
          }

          $em->persist($tarea);
          $em->flush();

          $response["resultado"] = "ok";
          $response["tarea"] = $tarea->getArray();
          $response["tarea"]["elementos"] = $em->getRepository('AppBundle:Red\Elemento')->findElementosTarea($tarea->getId());

        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/obra/{id}/tarea/{tareaId}/eliminar", name="red_gestionobra_obra__tarea_eliminar")
     */
    public function elementoEliminarAction(Request $request, $usuario_hash = "")
    {
      $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
      if(!$usuario) {
          throw $this->createNotFoundException();
      }

      $em = $this->getDoctrine()->getManager();

      $elementoRequest = json_decode($request->getContent(), true);
      $tarea = $em->getRepository("AppBundle:Red\Obra\Tarea")->find($elementoRequest["tareaId"]);
      $response = ["resultado" => "no ".count($tarea->getElementos())];

      if ($tarea && count($tarea->getElementos()) == 0) {

          // elimino el elemento
          $em->remove($tarea);
          $em->flush();

          $response = ["resultado" => "ok"];
      }

      return new JsonResponse($response);
    }

    /**
     * @Route("/obra/{id}/tarea/{tareaId}/elemento/{tareaElementoId}/finalizar", name="red_gestionobra_obra__tarea_elemento_finalizar")
     */
    public function finalizarrTareaElementoAction(Request $request, $usuario_hash = "")
    {
      $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
      if(!$usuario) {
          throw $this->createNotFoundException();
      }

      $response = array("resultado" => "no");
      $elementoRequest = json_decode($request->getContent(), true);

      $em = $this->getDoctrine()->getManager();
      $tareaElemento = $em->getRepository("AppBundle:Red\Obra\TareaElemento")->find($elementoRequest["tareaElementoId"]);

      if ($tareaElemento) {

        if ($tareaElemento->getTarea()->getTipo() == Entity\Red\Obra\Tarea::TIPO_FUSION
              || $tareaElemento->getTarea()->getTipo() == Entity\Red\Obra\Tarea::TIPO_INSTALACION_FUSION) {

          $elementoEstado = Entity\Red\Elemento\Estado::ESTADO_FUSIONADO;
          $ne = new Entity\Red\Elemento\Estado($elementoEstado);
          $ne->setCreadaPor($usuario);
          $ne->setElemento($tareaElemento->getElemento()->getElemento());

          $tareaElemento->getElemento()->getElemento()->setEstadoActual($ne);

          $em->persist($ne);
          $em->flush();

          // si el padre estaba iluminado tambièn se deberìa iluminar el elemento
          if ($tareaElemento->getElemento()->getElemento()->getParent() && $tareaElemento->getElemento()->getElemento()->getParent()->getEstadoActual()->getEstado() == Entity\Red\Elemento\Estado::ESTADO_ILUMINADO) {

            $elementoEstado = Entity\Red\Elemento\Estado::ESTADO_ILUMINADO;
            $ne = new Entity\Red\Elemento\Estado($elementoEstado);
            $ne->setElemento($tareaElemento->getElemento()->getElemento());
            $ne->setCreadaPor($usuario);

            $tareaElemento->getElemento()->getElemento()->setEstadoActual($ne);

            $em->persist($ne);
            $em->flush();

          }

        } else {

          $elementoEstado = Entity\Red\Elemento\Estado::ESTADO_INSTALADO;
          $ne = new Entity\Red\Elemento\Estado($elementoEstado);
          $ne->setElemento($tareaElemento->getElemento()->getElemento());
          $ne->setCreadaPor($usuario);

          $tareaElemento->getElemento()->getElemento()->addEstado($ne);

        }

        $tareaElemento->setFinalizadaPor($usuario);
        $tareaElemento->setFechaFinalizada(new \DateTime());

        $tareaElemento->setEstado(Entity\Red\Obra\TareaElemento::ESTADO_FINALIZADA);
        $em->flush();

        $response = array("resultado" => "ok", "elementoEstado" => $elementoEstado);

      } else {

        $response = array("resultado" => 2);
      }

      return new JsonResponse($response);
    }

}
