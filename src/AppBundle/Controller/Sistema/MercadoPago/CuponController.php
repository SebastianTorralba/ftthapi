<?php

namespace AppBundle\Controller\Sistema\MercadoPago;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity as Entity;
use AppBundle\Form as Form;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/{usuario_hash}/mp/cupon/sfhiweufrhiwegrasdas-werb")
 * @ParamConverter("usuario", class="AppBundle:Usuario", options={"mapping": {"usuario_hash" = "hash"}})
 */
class CuponController extends Controller
{
    /**
     * @Route("/crear/{id}", name="cupon_crear")
     */
    public function crearAction($id = null, Request $request)
    {                        
        $data = array("a" => 1);
        
        if(!$id) {
            return new Response("ID cupon no valido");                          
            exit();                    
        }
        
        $em = $this->getDoctrine()->getManager();
        $cupon = $em->getRepository('AppBundle:MercadoPagoCupon')->findOneBy(array("id" => $id));
        
        if (!$cupon) {
            return new Response("NO se encontro cupon cargado: ".$id);                          
            exit();                    
        }

        if ($cupon->getEstados()->exists(function($k,$e){ return $e->getNombre() == 'CREADO'; })) {
            return new Response("Ya hay creado un cupon de pago");                          
            exit();                    
        }        
        
        if (count($cupon->getItems()) < 1) {
            return new Response("NO se pudo crear cupon, no existen items");                          
            exit();        
        }        

        $mp = $this->get('app.mercado_pago');       
        $checkout = $mp->crearPreferencia($cupon);        
        
        $nuevoEstado = new Entity\MercadoPagoCupon\MercadoPagoCuponEstado();
        $nuevoEstado->setNombre("CREADO");
        $nuevoEstado->setDescripcion("Cupon de pago CREADO");
        $nuevoEstado->setInformacionExtra(json_encode($checkout));        
        $cupon->addEstado($nuevoEstado);
        
        if(isset($checkout['sandbox_init_point'])) {
            $cupon->setUrlPrueba($checkout['sandbox_init_point']);
        }
            
        if(isset($checkout['init_point'])) {
            $cupon->setUrl($checkout['init_point']);
        }        
                
        $em->flush();
        
        return new Response("Cupon de Pago CREADO");                          
        exit();        
    }      
    
    /**
     * @Route("/ver/{id}", name="cupon_ver")
     */
    public function getPreferenciaAction($id = null, Request $request)
    {     
        $mp = $this->get('app.mercado_pago');       

        return new JsonResponse($mp->getPreferencia($id));
    }    
    
    /**
     * @Route("/modificar/{id}/{idCupon}", name="cupon_modificar")
     */
    public function modificarPreferencia($id = null,$idCupon = null)
    {     
        $mp = $this->get('app.mercado_pago');       

        if ($id && $idCupon) {       
            
            $em = $this->getDoctrine()->getManager();
            $cupon = $em->getRepository('AppBundle:MercadoPagoCupon')->findOneBy(array("id" => $idCupon));

            if ($cupon) {

                $mp->modificarPreferencia($id, $cupon);
                return new JsonResponse($mp->getPreferencia($id));       
            }
        } 
        
        return new Response("ERROR NO EXISTE ID O CUPÓN");
    }     
}
