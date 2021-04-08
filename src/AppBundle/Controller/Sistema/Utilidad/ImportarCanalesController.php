<?php

namespace AppBundle\Controller\Sistema\Utilidad;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity as Entity;
use AppBundle\Form as Form;

/**
 * @Route("/utilidad/view")
*/
class ImportarCanalesController extends Controller
{
    /**
     * @Route("/importar/canales/ftp", name="importar_canales_ftp")
     */
    public function importarCanalesFtpAction(Request $request)
    {
//        $em = $this->getDoctrine()->getManager();
//        $conexion = $em->getConnection();
//        
//        $conn_id = ftp_connect("ftp.filestv.com.ar") or die("No se pudo conectar "); ;
//        $login_result = ftp_login($conn_id, "colsecor_nuevo", "larioja123");
//        ftp_pasv($conn_id, true);
//        $canales = ftp_nlist($conn_id, ".");
//
//        foreach($canales as $canalArchivo) {
//            
//            $canalArchivoArray = explode(".", $canalArchivo);
//            $canalNombre = $canalArchivoArray[0];
//            
//            $consulta = $conexion->prepare("INSERT INTO view_tv_canales (nombre,numero,imagen) values ('".$canalNombre."',0,'".str_replace(" ", "", $canalNombre).".png');");
//            $consulta->execute();
//        }        
        
        return new Response("ok");
    }            
}
