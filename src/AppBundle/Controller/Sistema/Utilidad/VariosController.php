<?php

namespace AppBundle\Controller\Sistema\Utilidad;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity as Entity;
use AppBundle\Form as Form;

/**
 * @Route("/{usuario_hash}/utilidad/varios")
*/
class VariosController extends Controller
{
    /**
     * @Route("/generar-icon-red-elemento-tipo/{id}", name="generar_icon_red_elemento_tipo")
     */
    public function generarIconRedElementoTipoAction($id = 0, Request $request)
    {
      $response = "error";
      $em = $this->getDoctrine()->getManager();

      $tipoElemento = $em->getRepository("AppBundle:Red\Topologia\ElementoTipo")->find($id);

      if ($tipoElemento) {

        $estadosColores = array_flip(Entity\Red\Elemento\Estado::$estadosColores);

        for ($i = 8; $i < 17; $i=$i+8) {

          $dimension = $i;
          $scale = ($dimension/ 32);

          foreach ($estadosColores as $key => $value) {

            $estadoColor = $value;
            $svg = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$dimension.' '.$dimension.'" preserveAspectRatio="none" width="'.$dimension.'" height="'.$dimension.'" >
                      <g transform="scale('.$scale.')">
                        <path x="0" y="0" fill="#'.$tipoElemento->getColorHexa().'" d="'.$tipoElemento->getSvgPath().'" />
                      </g>
                      <circle fill="'.$estadoColor.'" stroke="white"  cx="80%" cy="80%" r="20%"/>
                    </svg>';

            $png = new \Imagick();
            $png->setResolution($dimension, $dimension);
            $png->setBackgroundColor(new \ImagickPixel("transparent"));
            $png->readImageBlob($svg);
            $png->setImageFormat("png32");

            $fp = fopen('../web/uploads/imagenes/tipo-elemento/'.$id.'_'.$key."_".$dimension.'.png','w');
            $png->writeImageFile($fp);
            fclose($fp);

          }

          $estados_extras = [
            ["nombre" =>"null", "color" => "#444"],
            ["nombre" =>"base", "color" => "#".$tipoElemento->getColorHexa()],
          ];

          foreach ($estados_extras as $value) {

            $svg = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$dimension.' '.$dimension.'" preserveAspectRatio="none" width="'.$dimension.'" height="'.$dimension.'" >
                      <g transform="scale('.$scale.')">
                        <path x="0" y="0" fill="'.$value["color"].'" d="'.$tipoElemento->getSvgPath().'" />
                      </g>
                    </svg>';

            $png = new \Imagick();
            $png->setResolution($dimension, $dimension);
            $png->setBackgroundColor(new \ImagickPixel("transparent"));
            $png->readImageBlob($svg);
            $png->setImageFormat("png32");

            $fp = fopen('../web/uploads/imagenes/tipo-elemento/'.$id."_".$value["nombre"]."_".$dimension.'.png','w');
            $png->writeImageFile($fp);
            fclose($fp);

          }

        }

        $response = "ok";
      }

      return new Response($response);
    }
}
