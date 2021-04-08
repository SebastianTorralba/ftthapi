<?php

namespace AppBundle\Controller\Sistema;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity as Entity;
use AppBundle\Form as Form;

class PreVentaController extends Controller
{
    /**
     * @Route("/{usuario_hash}/preventa/producto", name="preventa_producto")
     * @ParamConverter("usuario", class="AppBundle:Usuario", options={"mapping": {"usuario_hash" = "hash"}})
     */ 
    public function preventaProductoAction(Entity\Usuario $usuario = null, Request $request)
    {
        if(! $this->get('app.herramienta')->validarUsuario($usuario)) {
            throw $this->createNotFoundException();
        }        
        
        $em = $this->getDoctrine()->getManager();        
        
        $resultado = $this->preventa($usuario, $request);
        $form        = $resultado["form"];
        $itemsSeleccioandos = $resultado["itemsSeleccioandos"];
        
        $productosDetalle[32] = $em->getRepository('AppBundle:ProductoDetalle')->find(12248);
        $productosDetalle[50] = $em->getRepository('AppBundle:ProductoDetalle')->find(13288);        
        $productosDetalle[55] = $em->getRepository('AppBundle:ProductoDetalle')->find(12249);        
        $productosDetalle[65] = $em->getRepository('AppBundle:ProductoDetalle')->find(13289);        
        $productosDetalle[75] = $em->getRepository('AppBundle:ProductoDetalle')->find(13290);                
        
        if ($resultado["resultado"]) {
            $this->get("session")->getFlashBag()->add("subscripcion-ok", "Su subscripción fue exitosa, nos comunicaremos a la brevedad");            
            return $this->redirect($this->generateUrl("preventa_producto",["usuario_hash" => $usuario->getHash()]));
        }        

        return $this->render('sistema/preventa.html.twig', array(
            'form'               => $form->createView(),
            'itemsSeleccioandos' => $itemsSeleccioandos,
            'productosDetalle'     => $productosDetalle,
            "usuario" => $usuario
        ));        
    }        
    
    public function preventa($usuario=null, $request)
    {
        $resultado = 0;
        $preventa = new Entity\PreventaInternet();        
        $preventa->setTerminos(true); // en el caso de la preventa siempre acepta los términos        
        
        if ($request->request->get('producto-tv-32')) {                    
            
            $cantidad = $request->request->get('producto-tv-32');
            
            if ($cantidad > 0) {
                $item = new Entity\PreventaInternet\PreventaInternetItem();
                $item->setNombre("TV 32");
                $item->setCantidad($cantidad);
                $item->setTipo("producto-tv"); 
                $preventa->addPreventaInternetItem($item);  
                $preventa->setCantidad($cantidad);                
            }
        }

        if ($request->request->get('producto-tv-50')) {                    
                
            $cantidad = $request->request->get('producto-tv-50');
                
            if ($cantidad > 0) {                
                $item = new Entity\PreventaInternet\PreventaInternetItem();
                $item->setNombre("TV 50");
                $item->setCantidad($cantidad);                
                $item->setTipo("producto-tv"); 
                $preventa->addPreventaInternetItem($item);     
                $preventa->setCantidad($preventa->getCantidad() + $cantidad);                                
            }    
        }       
        
        if ($request->request->get('producto-tv-55')) {                    
                
            $cantidad = $request->request->get('producto-tv-55');
                
            if ($cantidad > 0) {                
                $item = new Entity\PreventaInternet\PreventaInternetItem();
                $item->setNombre("TV 55");
                $item->setCantidad($cantidad);                
                $item->setTipo("producto-tv"); 
                $preventa->addPreventaInternetItem($item);     
                $preventa->setCantidad($preventa->getCantidad() + $cantidad);                                
            }    
        } 

        if ($request->request->get('producto-tv-65')) {                    
                
            $cantidad = $request->request->get('producto-tv-65');
                
            if ($cantidad > 0) {                
                $item = new Entity\PreventaInternet\PreventaInternetItem();
                $item->setNombre("TV 65");
                $item->setCantidad($cantidad);                
                $item->setTipo("producto-tv"); 
                $preventa->addPreventaInternetItem($item);     
                $preventa->setCantidad($preventa->getCantidad() + $cantidad);                                
            }    
        } 

        if ($request->request->get('producto-tv-75')) {                    
                
            $cantidad = $request->request->get('producto-tv-75');
                
            if ($cantidad > 0) {                
                $item = new Entity\PreventaInternet\PreventaInternetItem();
                $item->setNombre("TV 55");
                $item->setCantidad($cantidad);                
                $item->setTipo("producto-tv"); 
                $preventa->addPreventaInternetItem($item);     
                $preventa->setCantidad($preventa->getCantidad() + $cantidad);                                
            }    
        }         
        
        $preventa->setUsuarioCreo($usuario);
        
        $form = $this->createForm(Form\PreventaInternetType::class, $preventa);                          
        $itemsSeleccioandos = array();        
        $form->handleRequest($request);
        
        if ($form->isValid()) {                        

            $em = $this->getDoctrine()->getManager();
            
            if ($request->request->get('producto-tv-medio-de-pago')) {            
                foreach ($request->request->get('producto-tv-medio-de-pago') as $medioDePago) {

                    $item = new Entity\PreventaInternet\PreventaInternetItem();
                    $item->setNombre($medioDePago);
                    $item->setTipo("producto-tv-medio-de-pago");  

                    $itemsSeleccioandos[] = $item;                

                    $preventa->addPreventaInternetItem($item);                
                }                        
            }   

            $em->persist($preventa);            
            $em->flush();
            
            $resultado = 1;
        }
        
        return array(
            'form'               => $form,
            'itemsSeleccioandos' => $itemsSeleccioandos,
            'resultado'          => $resultado,                        
        );
    }       
}
