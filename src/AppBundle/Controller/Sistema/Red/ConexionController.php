<?php

namespace AppBundle\Controller\Sistema\Red;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use AppBundle\Entity as Entity;
use AppBundle\Form as Form;

/**
 * @Route("/{usuario_hash}/red/conexion")
*/
class ConexionController extends Controller
{
    /**
     * @Route("/filtro", name="red_conexion_filtro")
     */
    public function conexionesAction(Request $request, $usuario_hash)
    {
        $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
        if(!$usuario) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $response = array();

        $response = array(
            "conexiones" => [],
            "seguirCargando" => 0
        );

        $params["servicios"] = $request->query->has("s") ? $request->query->get("s") : "";
        $params["estados"]   = $request->query->has("e") ? $request->query->get("e") : "";
        $params["poligono"]  = $request->query->has("p") ? $request->query->get("p") : "";
        $params["offset"]  = $request->query->has("offset") ? $request->query->get("offset") : 9999999;

        $conexiones = $em->getRepository('AppBundle:Conexion')->findForRedTopologiaByParams($params);

        if ($conexiones) {

          $geotools = $this->get('app.geotools');

          // genero poligono
          $i = 0;
          $poligono = [];
          $poligonoPuntos = explode(";", $params["poligono"]);

          foreach($poligonoPuntos as $poligonoPunto) {

            $poligonoCoordenada = explode(",",$poligonoPunto);
            $poligono[$i] = [$poligonoCoordenada[0], $poligonoCoordenada[1]];

            $i++;
          }

          $i = 0;
          foreach($conexiones as $conexion) {

            if ($conexion->getInmueble() &&
                  $conexion->getInmueble()->getLatitud() && $conexion->getInmueble()->getLongitud() &&
                  $geotools->puntoEnPoligono([$conexion->getInmueble()->getLatitud(), $conexion->getInmueble()->getLongitud()], $poligono)) {

              $response["conexiones"][$i] = $conexion->getElementoArray();
              $i++;
            }
          }

          $response["seguirCargando"] = 1;
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/elemento/{id}", name="red_disenio_elemento")
     */
    public function elementoAction($id = 0, $usuario_hash)
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

        $elemento = $em->getRepository('AppBundle:Red\Topologia\Elemento')->find($id);
        if ($elemento) {
          $response["elemento"] = $elemento->getElementoArray();
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/eliminar/elemento", name="red_disenio_elemento_eliminar")
     */
    public function elementoEliminarAction(Request $request, $usuario_hash)
    {
      $usuario = $this->get('app.herramienta')->validarUsuario($usuario_hash);
      if(!$usuario) {
          throw $this->createNotFoundException();
      }

      $em = $this->getDoctrine()->getManager();

      $elementoRequest = json_decode($request->getContent(), true);
      $elemento = $em->getRepository("AppBundle:Red\Topologia\Elemento")->find($elementoRequest["id"]);
      $response = ["resultado" => "no"];

      if ($elemento) {

         // como voy a eliminar el elemento, le asigno su padre como padre a sus hijos
          foreach($elemento->getChildren() as $child){
            $child->setParent($elemento->getParent());
            $em->flush();
          }

          // para que se actualice los child asociados al objeto
          $elemento = $em->getRepository("AppBundle:Red\Topologia\Elemento")->find($elementoRequest["id"]);

          // elimino el elemento
          $em->remove($elemento);
          $em->flush();

          $response = ["resultado" => "ok"];
      }

      return new JsonResponse($response);
    }
}
