<?php

namespace AppBundle\Controller\Sistema\MercadoPago;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Entity as Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/mp/notificar/sfhiweufrhiwegrwerb")
*/
class NotificarController extends Controller
{
    /**
     * @Route("/axcewwfgy-la-le-r2-2", name="notificacion_crear_desde_mercado_pago")
     */
    public function notificarMercadoPagoAction(Request $request)
    {                
        $id = $request->request->has('id') ? $request->request->get('id') : 0;
        $topic = $request->request->has('topic') ? $request->request->get('topic') : '';           
        
        if ($request->query->has('topic')) {
            $topic = $request->query->get('topic');
        }

        if ($request->query->has('id')) {
            $id = $request->query->get('id');
        }        
                
        if ($this->recibirNotificacion($id,$topic)) {
            return new Response('', '200');
        } else {
            return new Response('', '201');
        }
    }        

    /**
     * @Route("/procesar/notificacion/mp/d1i2e3/{id}/{topic}", name="notificacion_crear_manualmente")
     */
    public function procesarNotificacionMpManualmenteAction($id = 0, $topic= '')
    {                
        if ($this->recibirNotificacion($id,$topic)) {
            return new Response('', '200');
        } else {
            return new Response('', '201');
        }                
    }    

    public function recibirNotificacion($id = 0, $topic= '') 
    {
        $fhRecibido = new \DateTime('NOW');                
        $em = $this->getDoctrine()->getManager();                    
        usleep(rand(2500, 5000));
        
        $nuevaNotificacion = new Entity\MercadoPagoNotificacion();
        $nuevaNotificacion->setIdMp($id);
        $nuevaNotificacion->setTopicMp($topic);
        $nuevaNotificacion->setEstado("RECIBIDA");
        $nuevaNotificacion->setFechaHoraRecibida($fhRecibido->format('Y-m-d H:i:s'));
        $em->persist($nuevaNotificacion);
        $em->flush();
        
        $this->procesarNotificacionMpMerchant();
    }      
    
    /**
     * @Route("/procesar/notificacion/mp/d1ise1/todas", name="notificacion_procesar_todas")
     */
    public function procesarNotificacionTodasAction()
    {                
        $this->procesarNotificacionMpMerchant();  
        
        return new Response('', '200');
    }      
    
    public function procesarNotificacionMpMerchant()
    {                
        $em = $this->getDoctrine()->getManager();            
        $mpService = $this->get('app.mercado_pago');
        
        usleep(rand(2500, 4000));
        
        $mp = $mpService->getMp();        
        $idCupon = 0;        
        $merchant_order_info = '';

        $notificacionEnProceso = $em->getRepository('AppBundle:MercadoPagoNotificacion')->findOneBy(array("estado" => "ENPROCESO"), array("fechaHora" => "ASC"));                          
        
        if (!$notificacionEnProceso) {
        
            $notificacion = $em->getRepository('AppBundle:MercadoPagoNotificacion')->findOneBy(array("estado" => "RECIBIDA"), array("fechaHora" => "ASC"));                          

            if ($notificacion) {
                
                $notificacion->setEstado("ENPROCESO");
                $em->flush();
                
                $id = $notificacion->getIdMp();
                $topic = $notificacion->getTopicMp();
                
                $f = fopen('log/'.$this->getEntorno().'log_mp.txt', 'a');                     
                fwrite($f, chr(10).date('d-m-Y H:i').'****ID:'.$id.'*****TOPIC:'.$topic.'****ENTORNO:'.$this->getEntorno().'***Credenciales:'.$mpService->getCredenciales().'*********************************************'.chr(10));                                 
                
                // verifico que existan los parametros de mercado pago mediante su id de operación
                if ($id != 0 && $topic != '') {     

                    if($topic == 'payment'){                
                        $payment_info = $mp->get("/collections/notifications/".$id);
                        $merchant_order_info = $mp->get("/merchant_orders/".$payment_info["response"]["collection"]["merchant_order_id"]);
                    // Get the merchant_order reported by the IPN.
                    } else if($topic == 'merchant_order'){
                        $merchant_order_info = $mp->get("/merchant_orders/".$id);
                    }

                    if ($merchant_order_info["status"] == 200) {


                            $idCupon = $merchant_order_info["response"]["external_reference"];                    
                            // traigo la venta sobre la cual se realizo la operacion
                            $msj = "BUSCANDO CUPON DE PAGO ".$idCupon;
                            echo $msj;
                            fwrite($f, chr(10).$msj);                             

                            $cupon = $em->getRepository('AppBundle:MercadoPagoCupon')->findOneBy(array("id" => $idCupon));                

                            if (!$cupon) {
                                
                                $fhProcesado = new \DateTime('NOW');    
                                $notificacion->setEstado("PROCESADA");                                    
                                $notificacion->setFechaHoraProcesada($fhProcesado->format('Y-m-d H:i:s'));
                                $em->flush();
                                
                                $msj = "NO SE ENCONTRO ".$idCupon;
                                echo $msj;                        
                                fwrite($f, chr(10).$msj);  

                                $this->procesarNotificacionMpMerchant();                                                                
                                
                                return 0;
                            }                    

                            $msj = "SI CUPON DE PAGO ".$idCupon;
                            echo $msj;
                            fwrite($f, chr(10).$msj);                                                         
                            
                            // If the payment's transaction amount is equal (or bigger) than the merchant_order's amount you can release your items 
                            $paid_amount = 0;
                            $iNuevoMovimiento = 0;

                            foreach ($merchant_order_info["response"]["payments"] as  $payment) {

                                $msj = "PAGO ID:".$payment['id'].', ESTADO:'.$payment['status'].', MONTO:'.$payment['transaction_amount'];
                                echo $msj;                        
                                fwrite($f, chr(10).$msj);                          

                                $pid = $payment['id'];
                                $pstatus = $payment['status'];
                                
                                usleep(1000); 
                                $movimiento = $em->getRepository('AppBundle:MercadoPagoCupon\MercadoPagoCuponMovimiento')->findOneBy(array("idMovimiento" => $payment['id'], "estado" => $payment['status']));                                                                          
                                
                                
                                $moi_payment_info = $payment_info = $mp->get("/collections/notifications/".$payment['id']);                                
                                
                                if (!$movimiento) {

                                    $msj = "NUEVO MOVIMIENTO";
                                    echo $msj;                        
                                    fwrite($f, chr(10).$msj);                                                      

                                    $nuevoMovimiento[$iNuevoMovimiento] = new Entity\MercadoPagoCupon\MercadoPagoCuponMovimiento();
                                    $nuevoMovimiento[$iNuevoMovimiento]->setIdMovimiento($payment['id']);
                                    $nuevoMovimiento[$iNuevoMovimiento]->setEstado($payment['status']);
                                    $nuevoMovimiento[$iNuevoMovimiento]->setEstadoDetalle($payment['status_detail']);
                                    $nuevoMovimiento[$iNuevoMovimiento]->setMedio($moi_payment_info["response"]["collection"]["payment_type"]);
                                    $nuevoMovimiento[$iNuevoMovimiento]->setTipo("payment");
                                    $nuevoMovimiento[$iNuevoMovimiento]->setMonto($payment['transaction_amount']);
                                    $nuevoMovimiento[$iNuevoMovimiento]->setMontoFinal($payment['total_paid_amount']);
                                    $nuevoMovimiento[$iNuevoMovimiento]->setMontoFinalDevuelto($payment['amount_refunded']);
                                    
                                    $nuevoMovimiento[$iNuevoMovimiento]->setInformacionExtra(json_encode($moi_payment_info));
                                    $nuevoMovimiento[$iNuevoMovimiento]->setMercadoPagoCupon($cupon);
                                    $nuevoMovimiento[$iNuevoMovimiento]->setMercadoPagoNotificacion($notificacion);
                                                                        
                                    $cupon->addMovimiento($nuevoMovimiento[$iNuevoMovimiento]);
                                    
                                    if ($payment['status'] == 'refunded') {
                                        $movimientoQueSeDevuelve = $em->getRepository('AppBundle:MercadoPagoCupon\MercadoPagoCuponMovimiento')->findOneBy(array("idMovimiento" => $payment['id'], "estado" => 'approved')); 
                                        
                                        if ($movimientoQueSeDevuelve) {
                                            $movimientoQueSeDevuelve->setMontoDevuelto($payment['transaction_amount']);
                                            $movimientoQueSeDevuelve->setMontoFinalDevuelto($payment['total_paid_amount']);                                        
                                        }
                                    }
                                    
                                    $em->flush();

                                    $iNuevoMovimiento++;
                                    usleep(2000);                                    
                                    
                                } else if (((float)$movimiento->getMontoFinalDevuelto() < (float)$payment['amount_refunded']) && $topic == 'merchant_order') {

                                    $movimiento->setMontoFinalDevuelto($payment['amount_refunded']); 
                                    $movimiento->setInformacionExtra(json_encode($moi_payment_info));
                                    $movimiento->setMercadoPagoNotificacion($notificacion);                                                         
                                    
                                    $porcentajeDelMontoDevuelto = ($movimiento->getMontoFinalDevuelto()*100)/$movimiento->getMontoFinal();
                                    $montoDevuelto = ($movimiento->getMonto() * $porcentajeDelMontoDevuelto)/100;
                                    $movimiento->setMontoDevuelto(round($montoDevuelto, 2));   
                                    
                                    $em->persist($movimiento);
                                    $em->flush();                                    
                                    usleep(1000);                                    
                                    
                                    $msj = "MODIFICO MOVIMIENTO: ".$movimiento->getId().'-'.$payment['amount_refunded'].'-'.$movimiento->getMontoFinalDevuelto();
                                    echo $msj;                        
                                    fwrite($f, chr(10).$msj);                                           
                                }                                 

                            }                                        

                            if (count($cupon->getMovimientos()) > 0) {

                                $paid_amount = $cupon->getPagoTotal();

                                if($paid_amount == $merchant_order_info["response"]["total_amount"]) {
                                    $cupon->setResumenEstadoActualPago("PAGADO");                                    
                                } elseif($paid_amount > $merchant_order_info["response"]["total_amount"]) {
                                    $cupon->setResumenEstadoActualPago("PAGADO +");
                                } elseif($paid_amount == 0) {
                                    $cupon->setResumenEstadoActualPago("SIN PAGO");                            
                                } else {;
                                    $cupon->setResumenEstadoActualPago("PAGO PARCIAL");
                                }

                            } 

                            $nuevoEstado = new Entity\MercadoPagoCupon\MercadoPagoCuponEstado();
                            $nuevoEstado->setInformacionExtra(json_encode($merchant_order_info["response"]));                         
                            $nuevoEstado->setNombre("CUPON DE PAGO ".$merchant_order_info["response"]["status"]);
                            $nuevoEstado->setDescripcion("Sin descripción");                                                                
                            
                            if ($merchant_order_info["response"]["status"] == 'open' || $merchant_order_info["response"]["status"] == 'opened') {
                                $nuevoEstado->setNombre("CUPON DE PAGO ABIERTO");
                                $nuevoEstado->setDescripcion("Se abrio el cupon de pago");                         
                            }  elseif ($merchant_order_info["response"]["status"] == 'close' || $merchant_order_info["response"]["status"] == 'closed') {
                                $nuevoEstado->setNombre("CUPON DE PAGO CERRADO");
                                $nuevoEstado->setDescripcion("No se podran realizar operaciones");                                    
                            }  elseif ($merchant_order_info["response"]["status"] == 'expired') {
                                $nuevoEstado->setNombre("CUPON DE PAGO EXPIRADO");
                                $nuevoEstado->setDescripcion("Se vencio la fecha de validez del cupon");                                    
                            }                    

                            $msj = $nuevoEstado->getNombre();
                            echo $msj;                        
                            fwrite($f, chr(10).$msj);                                                      

                            $cupon->addEstado($nuevoEstado);                        
                            $em->flush();    
                            
                            // si el estado del cupon es PAGADO o PAGADO + verifico si genero la venta 
                            if ($cupon->getResumenEstadoActualPago() == "PAGADO" || 
                                    $cupon->getResumenEstadoActualPago() == "PAGADO +") {                    
                                $this->generarVenta($cupon);   
                            }                            
                    }               
                }

            
                $fhProcesado = new \DateTime('NOW');    
                $notificacion->setEstado("PROCESADA");    
                $notificacion->setInformacionExtra(json_encode($merchant_order_info));
                $notificacion->setFechaHoraProcesada($fhProcesado->format('Y-m-d H:i:s'));

                $em->flush();
                
                $msj = "TERMINO OK";
                echo $msj;                        
                fwrite($f, chr(10).$msj);               
                fclose($f);  
                
                
                $this->procesarNotificacionMpMerchant();                
            }
        }

        $notificacion = $em->getRepository('AppBundle:MercadoPagoNotificacion')->findOneBy(array("estado" => "RECIBIDA"), array("fechaHora" => "ASC")); 
        if (!$notificacion) {
            $this->notificarPago();
        }
        
        return 1;
    }
        
    /**
     * @Route("/ver-entorno", name="ver_entorno")
     */
    public function verEntornoAction()
    {                
        return new Response($this->getEntorno(), '201');           
    } 
    
    // devuelve el entorno en el que se esta ejecutando la aplicacion
    private function getEntorno() 
    {
        $sandbox = $this->getParameter('sandbox');
      
        return $sandbox ? 'prueba_' : 'produccion_';
    }    
    
    /**
     * @Route("/generar-venta/{idCupon}", name="generar_venta")
     */
    public function generarVentaAction($idCupon=0)
    {                
        $result = $this->generarVenta($idCupon);
        
        return new Response($result);
    }       
    
    public function generarVenta($idCupon = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();       
        
        $cupon = $em->getRepository('AppBundle:MercadoPagoCupon')->findOneBy(array("id" => $idCupon));        
        $fechaHora = new \DateTime('NOW');
        
        if (!$cupon) {
            return "No existe el cupón";
        }        
        
        if ($cupon->getVentaId()) {
            return "El cupón ya tiene una venta";
        }        
                
        // busco si existe un abonado con los datos del cupon
        $sqlAbonado = "select * from abonados where documento_numero=:documento_numero";        
        $statement = $connection->prepare($sqlAbonado);        
        $statement->bindValue('documento_numero', $cupon->getDni());
        $statement->execute();
        $abonado = $statement->fetchAll();           
        
        $idAbonado = 0;
        $idLocalidad = 666666;
        $codBarrio = 1;
        $codCalle = 1;
        $modoDomicilio = 1;
        
        if (count($abonado)) {
            $idAbonado = $abonado[0]["id_abonado"];
            $codBarrio = $abonado[0]["cod_barrio"];
            $codCalle = $abonado[0]["cod_calle"];
            $modoDomicilio = 0;            
        }         
                
        $clienteInsertArray = array(
            'apellido_nombre' => $cupon->getNombre(),
            'documento_tipo' => 1,
            'documento_numero' => $cupon->getDni(),
            'estado' => 1,
            'tipo_persona' => $cupon->getTipoPersona(),
            'cuil_cuit' => $cupon->getCuit(),
            'sexo' => $cupon->getSexo(),
            'e_mail' => $cupon->getEmail(),
            'condicion_iva' => $cupon->getCondicionIva(),
            'fecha_carga' => $fechaHora->format('Y-m-d H:i:s'),
            'id_usuario' => 0,
            'app_ventana' => 'generarVenta',
            'hostname' => 'SERVIDOR-WEB',
            'id_abonado' => $idAbonado,
            'telefono' => $cupon->getTelefono(),
            'modo_domicilio' => $modoDomicilio,
            'calle_nro' => $cupon->getDireccionCalle().' '.$cupon->getDireccionNumero(),
            'barrio' => $cupon->getDireccionBarrio(),
            'localidad' => $cupon->getDireccionLocalidad(),
            'cp' => $cupon->getDireccionCp(),
            'provincia' => $cupon->getDireccionProvincia(),
            'dom_numero' => $cupon->getDireccionNumero(),
            'cod_calle' => $codCalle,
            'cod_barrio' => $codBarrio,
            'id_localidad' => $idLocalidad
         );
        
        $clienteId = 0;
        $result = $connection->insert('clientes', $clienteInsertArray);
        if ($result > 0) {
            
            $clienteId = $connection->lastInsertId();                
            
            $ventaProductosInsertArray = array(
                'id_cliente' => $clienteId,
                'tipo_cliente' => 0,
                'id_producto' => 0,
                'id_dispositivo' => 0,
                'id_factura' => 0,
                'monto' => 0,
                'estado_venta' => $cupon->getEnviarDomicilio() ? 6 : 4,
                'enviar_domicilio' => $cupon->getEnviarDomicilio(),
                'lugar_trabajo' => "'s/datos'",
                'forma_pago' => 'VEN10',
                'fecha_carga' => $fechaHora->format('Y-m-d H:i:s'),
                'fecha_venta' => $fechaHora->format('Y-m-d H:i:s'),                
                'cod_sucursal' =>  $cupon->getEnviarDomicilio() ? 1 : $this->getCodSucursalUsuario($cupon->getIdUsuario()),
                'tipo_acuerdo' => 'VENTA',
                'id_usuario' => 0,
                'envioTarifaId' => $cupon->getEnvioTarifaId(),
             );            
            
            $ventaProductoId = 0;
            $totalPagado = 0;            
            $result = $connection->insert('venta_productos', $ventaProductosInsertArray);
            if ($result > 0) {
                
                $ventaProductoId = $connection->lastInsertId();
                
                $cupon->setVentaId($ventaProductoId);
                $em->flush();                
                
                foreach ($cupon->getItems() as $item) {
                        
                    $sqlAbonado = "select * from productos_detalle where id_dispositivo=:id_dispositivo";                    
                    $statement = $connection->prepare($sqlAbonado);        
                    $statement->bindValue('id_dispositivo', $item->getCodigo());
                    $statement->execute();
                    $productoDetalle = $statement->fetchAll();                       
                    
                    $precio_sapem = 0;
                    $idProducto = 0;
                    if ($productoDetalle) {
                        $precio_sapem = $productoDetalle[0]['monto'];
                        $idProducto = $productoDetalle[0]['id_producto'];
                    }
                    
                    $montoFinancia = $item->getMontoTotal();
                    
                    $ventaProductosDetailInsertArray = array(
                        'venta_productos_header' => $ventaProductoId,
                        'pro_det_precio_id' => $item->getCodigo(),
                        'id_producto' => $idProducto,
                        'id_dispositivo' => $item->getCodigo(),
                        'cantidad' => $item->getCantidad(),
                        'precio_sapem' => $precio_sapem,
                        'entrega' => 0,
                        'descuento' => 0,
                        'monto_financia' => $montoFinancia,
                        'cuotas' => 1,
                        'monto_cuotas' => $montoFinancia,                    
                        'monto_envio' => $item->getMontoEnvio(),                         
                        'cobranza_precio' => $montoFinancia,
                        'cobranza_amto' => 0,
                    );                            
                    
                    $result = $connection->insert('venta_productos_detail', $ventaProductosDetailInsertArray); 
                    $totalPagado += $item->getMontoTotal();
                }   

                $ventaProductosMediosInsertArray = array(
                    'venta_productos_header' => $ventaProductoId,
                    'medioCodigo' => 'MERPA',
                    'medioMonto' => $totalPagado,
                    'cuotas' => 1,
                    'medioMontoCuota' => $totalPagado
                );                            

                $result = $connection->insert('venta_productos_medios', $ventaProductosMediosInsertArray);                                                                                     
                                        
                // busco id en pktablas para número factura
                $sqlFacturaPk = "select identificador from pk_tablas where nombre_tabla=:nombre_tabla and cod_sucursal=1";        
                $statement = $connection->prepare($sqlFacturaPk);        
                $statement->bindValue('nombre_tabla', 'facturas');
                $statement->execute();
                $idFacturaPk = $statement->fetchAll();                 
                
                //actualizo el pk tabla de facturas
                $connection->update('pk_tablas', array("identificador" => ($idFacturaPk[0]["identificador"] + 1)),array("nombre_tabla" => "facturas"));  
                
                // busco secuencial factura
                $sqlSecuencial = "select max(factura_secuencial) as factura_secuencial from facturas where id_abonado=:id_abonado and id_conexion=:id_conexion";        
                $statement = $connection->prepare($sqlSecuencial);        
                $statement->bindValue('id_abonado', $idAbonado);
                $statement->bindValue('id_conexion', 0);
                $statement->execute();
                $secuencialFactura = $statement->fetchAll();                          
                
                $facturaInsertArray = array(
                    'id_abonado' => $idAbonado,
                    'id_conexion' => 0,
                    'factura_nro' => str_pad($idFacturaPk[0]["identificador"], 12, "0", STR_PAD_LEFT),
                    'factura_secuencial' => $secuencialFactura[0]["factura_secuencial"]+1,
                    'factura_fecha' => $fechaHora->format('Y-m-d H:i:s'),
                    'factura_estado' => 'FAC20',                    
                    'periodo' => $fechaHora->format('m')."/".$fechaHora->format('Y'),                    
                    'monto' => $totalPagado,
                    'fecha_emision' => $fechaHora->format('Y-m-d H:i:s'),
                    'fecha_carga' => $fechaHora->format('Y-m-d H:i:s'),                     
                    'fecha_vencimiento_1' => $fechaHora->add(new \DateInterval('P10D'))->format('Y-m-d H:i:s'),
                    'fecha_vencimiento_2' => $fechaHora->add(new \DateInterval('P15D'))->format('Y-m-d H:i:s'),
                    'secuencial_registro' => 1,
                    'id_usuario' => 0,
                    'app_ventana' => 'generarVenta',
                    'hostname' => 'SERVIDOR-WEB', 
                    'factura_tipo' => 'TVTDS',
                    'doc_tipo' => 'CPTPG',
                    'cliente_id' => $clienteId,
                );                            
                
                $result = $connection->insert('facturas', $facturaInsertArray);  
                $idFactura = 0;
                if ($result > 0) {                
                    $idFactura =  $connection->lastInsertId();
                                             
                    $facturaDetalleInsertArray = array(
                        'factura_fecha' => $facturaInsertArray["factura_fecha"],
                        'factura_secuencial' => $facturaInsertArray["factura_secuencial"],
                        'factura_nro' => $facturaInsertArray["factura_nro"],
                        'id_abonado' => $idAbonado,
                        'periodo' => $facturaInsertArray["periodo"],                    
                        'item_secuencial' => 1,                                            
                        'id_conexion' => 0,
                        'cod_concepto' => 'C78',
                        'monto' => $totalPagado,                            
                        'item_estado' => 0,                                                        
                        'fecha_carga' => $facturaInsertArray["fecha_carga"],
                        'cod_sucursal' => 1,
                        'id_conexion' => 0,
                        'id_dispositivo' => 1,
                        'facturas_id' => $idFactura,
                        'cae_generator' => "'IPT'",                            
                    );  
                    
                    $result = $connection->insert('facturas_detalle', $facturaDetalleInsertArray);                                          
                    $connection->update('venta_productos', array("id_factura" => $idFactura, "monto" => $totalPagado),array("id_venta_tv" => $ventaProductoId));  

                }                
            }            
        }
        
        return 1;
    }

    public function getCodSucursalUsuario($idUsuario = 1) 
    {
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();  
        
        $sqlUsuario = "select cod_sucursal from usuarios where id_usuario = :id_usuario";        
        $statement = $connection->prepare($sqlUsuario);        
        $statement->bindValue('id_usuario', $idUsuario);
        $statement->execute();
        $usuario = $statement->fetchAll();             
        
        if (count($usuario)>0) {
            return $usuario[0]["cod_sucursal"];
        }
        
        return 1;
    }
    
    
    /**
     * @Route("/notificar-pago", name="notificar_pago")
     */
    public function notificarPagoAction()
    {                
        $this->notificarPago();
        return new Response("", '200');           
    } 
    
    public function notificarPago() 
    {
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();   
        
        $sqlNotificarPago = "select * from MercadoPagoCupon mpc where (mpc.resumenEstadoActualPago = :estado or mpc.resumenEstadoActualPago = :estado2) and mpc.id not in (select mercadoPagoCupon_id from MercadoPagoCuponEstado where mercadoPagoCupon_id=mpc.id and nombre like '%AVISO DE PAGO ENVIADO%')";        
        $statement = $connection->prepare($sqlNotificarPago);        
        $statement->bindValue('estado', 'PAGADO');
        $statement->bindValue('estado2', 'PAGADO +');
        $statement->execute();
        $cupones = $statement->fetchAll();         
        
        $usuario = $em->getRepository("AppBundle:Usuario")->find(113);
        
        foreach ($cupones as $cupon) {
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->generateUrl("email_ventaproducto_enviaravisopagocupon", array("usuario_hash" => $usuario->getHash(),"idCupon" => $cupon["id"]), UrlGeneratorInterface::ABSOLUTE_URL));       
            $response = curl_exec($ch);                  
            curl_close($ch);  
            
            usleep(2000);
        }
        
        return 1;
    }
}