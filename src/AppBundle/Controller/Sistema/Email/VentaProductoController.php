<?php

namespace AppBundle\Controller\Sistema\Email;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use AppBundle\Entity as Entity;



/**
 * @Route("/{usuario_hash}/sistema/email/venta-producto")
 * @ParamConverter("usuario", class="AppBundle:Usuario", options={"mapping": {"usuario_hash" = "hash"}})
 */
class VentaProductoController extends Controller
{            
    /**
     * @Route("/enviar-cupon/{idCupon}/{vendedor}", name="email_ventaproducto_enviarcupon")
     */
    public function enviarCuponAction($idCupon=0, $vendedor = '')
    {        
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();        
        
        if(!$idCupon) {
            return new Response("No existe cupón");;
        }
        
        $queryC= "select 
                    mpc.id,mpc.nombre,mpc.email,mpc.url,mpc.urlPrueba,mpc.enviarDomicilio,mpc.sexo, mpc.direccionCalle, mpc.direccionNumero, mpc.direccionBarrio, mpc.direccionLocalidad, mpc.direccionCp, mpc.direccionProvincia
                from MercadoPagoCupon mpc
                where mpc.id=:idCupon";
        
        
        $statement = $connection->prepare($queryC);        
        $statement->bindValue('idCupon', $idCupon);
        $statement->execute();
        $resultsC = $statement->fetchAll();           

        if (!count($resultsC)) {
            return new Response("No existe cupón");;
        }       

        $queryI= "select 
                    mpci.nombre,mpci.cantidad,mpci.montoTotal,mpci.montoEnvio
                from MercadoPagoCuponItem mpci
                where mpci.mercadoPagoCupon_id=:idCupon";
        

        $statement = $connection->prepare($queryI);        
        $statement->bindValue('idCupon', $idCupon);
        $statement->execute();
        $resultsI = $statement->fetchAll();           
        
        if (!count($resultsI)) {
            return new Response("No existen items en cupón");
        }               
        
//        return $this->render('sistema/email/venta_productos/enviar_cupon.html.twig', array(
//            'cupon' => $resultsC[0],
//            'items' => $resultsI,
//            'vendedor' => $vendedor
//        ));    
        
        $sandbox = $this->getParameter('sandbox');        
        
        $body = $this->renderView('sistema/email/venta_productos/enviar_cupon.html.twig', array(
            'cupon' => $resultsC[0],
            'items' => $resultsI,
            'vendedor' => $vendedor,
            "sandbox" => $sandbox
        ));        
        
        $destinatario = $sandbox ?  'diegogardella86@gmail.com' : $resultsC[0]['email'];

        $mensaje = \Swift_Message::newInstance()
            ->setSubject('Cupón de pago IPT n° '.$idCupon)
            ->setFrom(array(
                'avisos@iparatodos.com.ar' => "Avisos Internet Para Todos",
            ))
            ->setTo($destinatario)                
            ->setBody($body, 'text/html')
        ;

        $result = $this->get('mailer')->send($mensaje);

        sleep(1);

        if ($result) {
            
            $cuponO = $em->getRepository('AppBundle:MercadoPagoCupon')->findOneBy(array("id" => $idCupon)); 
            if ($cuponO) {
                $nuevoEstado = new Entity\MercadoPagoCupon\MercadoPagoCuponEstado();
                $nuevoEstado->setInformacionExtra("");                         
                $nuevoEstado->setNombre("CUPON DE PAGO ENVIADO");
                $nuevoEstado->setDescripcion("Enviado a ".$resultsC[0]['email']);  
                $cuponO->addEstado($nuevoEstado);                        
                $em->flush();      
            }    
            
            return new Response('Email enviado a '.$resultsC[0]['email'], 200);
        }          
        
        return new Response('No se pudo enviar email a '.$resultsC[0]['email'], 200);        
    }     

    /**
     * @Route("/enviar-aviso-pago-cupon/{idCupon}", name="email_ventaproducto_enviaravisopagocupon")
     */
    public function enviarAvisoPagoCuponAction($idCupon=0)
    {        
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();        
        
        if(!$idCupon) {
            return new Response("No existe cupón");;
        }
        
        $queryC= "select 
            mpc.id,mpc.nombre,mpc.email,mpc.url,mpc.ventaId,mpc.enviarDomicilio,mpc.sexo,mpc.ventaId,mpc.ventaId, mpc.direccionCalle, mpc.direccionNumero, mpc.direccionBarrio, mpc.direccionLocalidad, mpc.direccionCp, mpc.direccionProvincia,mpc.id_usuario,
            u.cod_sucursal,s.descripcion as sucursal
          from MercadoPagoCupon mpc
          inner join usuarios u on u.id_usuario=mpc.id_usuario
          inner join sucursales s on s.cod_sucursal=u.cod_sucursal
          where mpc.id=:idCupon";
        
        $statement = $connection->prepare($queryC);        
        $statement->bindValue('idCupon', $idCupon);
        $statement->execute();
        $resultsC = $statement->fetchAll();           

        if (!count($resultsC)) {
            return new Response("No existe cupón");
        }       

        if (!$resultsC[0]) {
            return new Response("No existe venta");
        }       
        
        
        $queryI= "select 
                    mpci.nombre,mpci.cantidad,mpci.montoTotal,mpci.montoEnvio
                from MercadoPagoCuponItem mpci
                where mpci.mercadoPagoCupon_id=:idCupon";
        
        $statement = $connection->prepare($queryI);        
        $statement->bindValue('idCupon', $idCupon);
        $statement->execute();
        $resultsI = $statement->fetchAll();           
        
        if (!count($resultsI)) {
            return new Response("No existen items en cupón");
        }               
        
//        return $this->render('sistema/email/venta_productos/enviar_avisopagocupon.html.twig', array(
//            'cupon' => $resultsC[0],
//            'items' => $resultsI,
//        ));       
        
        $sandbox = $this->getParameter('sandbox');                
         
        $body = $this->renderView('sistema/email/venta_productos/enviar_avisopagocupon.html.twig', array(
            'cupon' => $resultsC[0],
            'items' => $resultsI,
            "sandbox" => $sandbox            
        ));        

        $destinatario = $sandbox ?  'diegogardella86@gmail.com' : $resultsC[0]['email'];        
        $mensaje = \Swift_Message::newInstance()
            ->setSubject('Cupón de pago IPT n° '.$idCupon)
            ->setFrom(array(
                'avisos@iparatodos.com.ar' => "Avisos Internet Para Todos",
            ))
            ->setTo($destinatario)
            ->setBody($body, 'text/html')
        ;

        $result = $this->get('mailer')->send($mensaje);

        sleep(1);               
        
        if ($result) {
            
            $cuponO = $em->getRepository('AppBundle:MercadoPagoCupon')->findOneBy(array("id" => $idCupon)); 
            if ($cuponO) {
                $nuevoEstado = new Entity\MercadoPagoCupon\MercadoPagoCuponEstado();
                $nuevoEstado->setInformacionExtra("");                         
                $nuevoEstado->setNombre("AVISO DE PAGO ENVIADO");
                $nuevoEstado->setDescripcion("Enviado a ".$resultsC[0]['email']);  
                $cuponO->addEstado($nuevoEstado);                        
                $em->flush();                                      
            }
             
            if ($resultsC[0]['enviarDomicilio']) {
                
                $destinatarios =  $sandbox ?  'diegogardella86@gmail.com' : ["carlos.valdez@internetparatodos.com.ar","mario.flores@internetparatodos.com.ar"];
                
                $mensaje2 = \Swift_Message::newInstance()
                    ->setSubject('ENVÍO DE PRODUCTO venta número '.$resultsC[0]["ventaId"])
                    ->setFrom(array(
                        'avisos@iparatodos.com.ar' => "Avisos Internet Para Todos",
                    ))
                    ->setTo($destinatarios)
                    ->setBody("ENVÍO DE PRODUCTO!! La venta número ".$resultsC[0]["ventaId"]." requiere que sea enviada a su correspondiente domicilio de destino.", 'text/html')
                ;
                
                $result2 = $this->get('mailer')->send($mensaje2);                 
            }
           
            
            return new Response('Email enviado a '.$resultsC[0]['email'], 200);
        }          
        
        return new Response('No se pudo enviar email a '.$resultsC[0]['email'], 200);        
    }   
    
   /**
     * @Route("/enviar-avisoproductoencamino/{id_venta_tv}", name="email_ventaproducto_enviaravisoproductoencamino")
     */
    public function enviarAvisoProductoEnCaminoAction($id_venta_tv=0)
    {        
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();        
        
        if(!$id_venta_tv) {
            return new Response("No existe cupón");;
        }
        
        $queryV= "select vp.id_venta_tv,c.cp,c.e_mail,c.sexo, c.id_cliente,c.apellido_nombre,c.documento_numero, c.calle_nro,c.barrio,c.localidad,c.provincia,vp.codigo_seguimiento_envio,ee.nombre,ee.url_web_seguimiento
                from venta_productos vp
                inner join Clientes c on c.id_cliente=vp.id_cliente
                inner join EnvioTarifa et on et.id=vp.envioTarifaId
                inner join EnvioUnidad eu on eu.id=et.unidad_id
                inner join EnvioEmpresa ee on ee.id=eu.empresa_id
                where id_venta_tv = :id_venta_tv";

        
        $statement = $connection->prepare($queryV);        
        $statement->bindValue('id_venta_tv', $id_venta_tv);
        $statement->execute();
        $resultsV = $statement->fetchAll();           

        if (!count($resultsV)) {
            return new Response("No existe venta");;
        }       

        $queryI= "select 
                vpd.cantidad,d.nombre_dispositivo
                from Venta_productos_detail vpd
                inner join dispositivos d on d.id_dispositivo=vpd.id_dispositivo
                where vpd.venta_productos_header = :id_venta_tv";
        

        $statement = $connection->prepare($queryI);        
        $statement->bindValue('id_venta_tv', $id_venta_tv);
        $statement->execute();
        $resultsI = $statement->fetchAll();           
        
        if (!count($resultsI)) {
            return new Response("No existen items en venta");
        }               
        
//        return $this->render('sistema/email/venta_productos/enviar_avisoproductosencamino.html.twig', array(
//            'venta' => $resultsV[0],
//            'items' => $resultsI
//        ));    
        
        $sandbox = $this->getParameter('sandbox');                
                      
        $body = $this->renderView('sistema/email/venta_productos/enviar_avisoproductosencamino.html.twig', array(
            'venta' => $resultsV[0],
            'items' => $resultsI,
            "sandbox" => $sandbox                   
        ));        
        
        $destinatario = $sandbox ?  'diegogardella86@gmail.com' : $resultsV[0]['e_mail'];    
        
        $mensaje = \Swift_Message::newInstance()
            ->setSubject('Sus productos están en camino')
            ->setFrom(array(
                'avisos@iparatodos.com.ar' => "Avisos Internet Para Todos",
            ))
            ->setTo($destinatario)
            ->setBody($body, 'text/html')
        ;

        $result = $this->get('mailer')->send($mensaje);

        sleep(1);

        if ($result) {
                        
            return new Response('Email enviado a '.$resultsV[0]['e_mail'], 200);
        }          
        
        return new Response('No se pudo enviar email a '.$resultsV[0]['e_mail'], 200);        
    }         
}
