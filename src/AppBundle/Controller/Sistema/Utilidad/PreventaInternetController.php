<?php

namespace AppBundle\Controller\Sistema\Utilidad;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity as Entity;
use AppBundle\Form as Form;

/**
 * @Route("/utilidad/preventa-internet")
*/
class PreventaInternetController extends Controller
{
    /**
     * @Route("/fix-utf8", name="preventa_internet_fix_utf8")
     */
    public function fixUtf8Action(Request $request)
    {
//        $em = $this->getDoctrine()->getManager();
//        $conexion = $em->getConnection();
//                                   
//        $consulta = $conexion->prepare("SELECT * FROM preventaInternet where id=0");
//        $consulta->execute();
//        $preventas = $consulta->fetchAll();
//
//        foreach($preventas as $preventa) {
//            $sql = "UPDATE preventaInternet SET nombre='".utf8_decode($preventa["nombre"])."', direccion='".utf8_decode($preventa["direccion"])."' WHERE id =".$preventa["id"];
//            $consulta = $conexion->prepare($sql);  
//            $consulta->execute();            
//            echo $preventa["id"]."--->".$preventa["nombre"]."--->".$preventa["direccion"].'<br>';
//        }
//        
//        exit();
        
        return new Response("ok");
    }            
}
