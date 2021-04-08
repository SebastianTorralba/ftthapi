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
 * @Route("/{usuario_hash}/red/apareo-nap-conexion")
*/
class ApareoNapConexionController extends Controller
{

  /**
   * @Route("/uh", name="red_apareonapconexionuh")
   */
  public function indexUhAction(Request $request, $usuario_hash = "")
  {
        $u = $this->getDoctrine()->getManager()->getRepository('AppBundle:Usuario')->find(113);
      echo $u->getHash();
      exit();

      return $this->render('sistema/red/apareo-nap-conexion.html.twig', [
        "usuario_hash" => $usuario_hash
      ]);
  }


    /**
     * @Route("/", name="red_apareonapconexion")
     */
    public function indexAction(Request $request, $usuario_hash = "")
    {
        $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
        if(!$usuario) {
            throw $this->createNotFoundException();
        }

        return $this->render('sistema/red/apareo-nap-conexion.html.twig', [
          "usuario_hash" => $usuario_hash
        ]);
    }

    /**
     * @Route("/elementos-red/", name="red_apareonapconexion_elementosred")
     */
    public function elementosRedAction(Request $request, $usuario_hash = "")
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

      $elementos = $em->getRepository('AppBundle:Red\Elemento')->findElementosPorTipo(6);

      if ($elementos) {
        $response["elementos"] = $elementos;
      }

      return new JsonResponse($response);
    }

    /**
     * @Route("/conexiones-pendientes", name="red_apareonapconexion_conexionespendientes")
     */
    public function conexionesFtthPendientesAction(Request $request, $usuario_hash = "")
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

      $params["servicios"] = $request->query->has("s") ? $request->query->get("s") : "";
      $params["estados"]   = $request->query->has("e") ? $request->query->get("e") : "";
      $params["operaciones"]   = $request->query->has("o") ? $request->query->get("o") : "";
      $params["conexion"]   = $request->query->has("c") ? $request->query->get("c") : "";

      $elementos = $em->getRepository('AppBundle:Conexion')->findConexionesFtthPendientes($params);

      if ($elementos) {
        $response["elementos"] = $elementos;
        $i = 0;
        foreach($elementos as $elemento) {
          $response["elementos"][$i]["id"] = $elemento["id_conexion"];
          $response["elementos"][$i]["latitud"] = (float)$elemento["latitud"];
          $response["elementos"][$i]["longitud"] = (float)$elemento["longitud"];
          $response["elementos"][$i]["resumen"]  = [
            "categoria" => "Conexiones",
            "elementos" => [[
              "id"          => "FTTH",
              "descripcion" => "FTTH",
              "estado"      => "Pendiente",
              "cantidad"    => 1,
              "unidad"      => ""
            ]]
          ];

          $i++;
        }
      }

      return new JsonResponse($response);
    }

    /**
     * @Route("/save/", name="red_apareonapconexion_save")
     */
    public function apareoSaveAction(Request $request, $usuario_hash = "")
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

      if(isset($elementoRequest["apareos"])) {

        foreach($elementoRequest["apareos"] as $elementoNuevo){

          $conexionReferencia = $em->getRepository("AppBundle:Conexion\Referencia")->findOneByConexion($elementoNuevo["conexion"]);

          if ($conexionReferencia) {

            $elementoRed = $em->getRepository("AppBundle:Red\Elemento")->find($elementoNuevo["elementoRed"]);

            if ($elementoRed) {
              $conexionReferencia->setNapSegundoNivel($elementoRed->getCodigo());
              $conexionReferencia->setNapPrimerNivel($elementoRed->getParent()->getCodigo());
              $conexionReferencia->setHabilitado('S');
              //$conexionReferencia->setComentario($elementoRed->getParent()->getCodigo().' - '.$elementoRed->getCodigo());

              $em->flush();
            }

          }

        }

        $response = [
          "resultado" => "ok",
        ];
      }

      return new JsonResponse($response);
    }
}
