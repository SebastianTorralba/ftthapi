<?php

namespace AppBundle\Controller\Sistema\Utilidad;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity as Entity;
use AppBundle\Form as Form;

/**
 * @Route("/{usuario_hash}/utilidad/googlemap")
 * @ParamConverter("usuario", class="AppBundle:Usuario", options={"mapping": {"usuario_hash" = "hash"}})
*/
class GoogleMapController extends Controller
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
     * @Route("/{offset}/oca-control-georeferencia-render", name="oca_control_georeferencia_render")
     */
    public function ocaControlGeoreferenciaRenderAction(Request $request, Entity\Usuario $usuario = null, $offset = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $response = array();

        if(! $this->get('app.herramienta')->validarUsuario($usuario)) {
            throw $this->createNotFoundException();
        }

        $where = '';
        $response = array(
            "conexiones" => []
        );

        $params = $request->query->all();

        if (count($params) > 0) {

            $conexiones = $em->getRepository('AppBundle:OcaGeoreferenciaConexion')->findByParams(($offset*1000),$params);

            $i = 0;
            foreach($conexiones as $conexion) {

                $response["conexiones"][$i]["id"] = $conexion->getIdConexion();
                $response["conexiones"][$i]["latitud"] = $conexion->getGeoreferenciaActual()->getLatitud();
                $response["conexiones"][$i]["longitud"] = $conexion->getGeoreferenciaActual()->getLongitud();
                $response["conexiones"][$i]["idConexion"]  = $conexion->getIdConexion();
                $response["conexiones"][$i]["fechaHoraGeoreferencia"]  = $conexion->getGeoreferenciaActual()->getFechaHoraGeoreferencia()->format('d/m/Y H:i:s');
                $response["conexiones"][$i]["title"]  = "CNX ".$conexion->getIdConexion()." - ".$conexion->getGeoreferenciaActual()->getFechaHoraGeoreferencia()->format('d/m/Y H:i:s');

                $i++;
            }

        }

        $response["offset"] = $offset + 1;

        return new JsonResponse($response);
    }

    /**
     * @Route("/oca-control-georeferencia", name="oca_control_georeferencia_2")
     */
    public function ocaControlGeoreferenciaAction(Request $request, Entity\Usuario $usuario = null)
    {
        if(! $this->get('app.herramienta')->validarUsuario($usuario)) {
            throw $this->createNotFoundException();
        }

        $params = "";
        foreach ($request->query->all() as $key => $value) {
            $params .= $key."=".$value."&";
        }

        $href = $this->generateUrl('oca_control_georeferencia_render', array(
            "usuario_hash" => $usuario->getHash(),
            "offset"       => "*offset*"
        ), UrlGeneratorInterface::ABSOLUTE_URL);

        $href .= "?".$params;


        return $this->render('sistema/utilidad/googlemap/oca_control_georeferencia.html.twig', [
          "href" => $href
        ]);
    }

}
