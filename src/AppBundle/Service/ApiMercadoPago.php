<?php

namespace AppBundle\Service;

use \Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Service\MercadoPago\sdk\lib as MercadoPago;

/*
 * IMPLEMENTADO USANDO EL CHECKOUT BÁSICO
 * https://www.mercadopago.com.ar/developers/es/solutions/payments/basic-checkout/test/basic-sandbox/
 * Con la cuenta de la rioja telecomunicaciones
 */

class ApiMercadoPago
{
    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * USUARIO DE MERCADO PAGO ENTORNO PRODUCCIÓN
     * Cliente id de Mercado Pago
     * Cliente secret de Mercado Pago
     * @var string
     */
    private $client_id = '7309195889025220';
    private $client_secret = 'j13E0fdr6C9UudiaUfuUn2A2l4h8EY4q';

    /**
     * USUARIO DE MERCADO PAGO ENTORNO DESARROLLO PRUEBA
     * Cliente id de Mercado Pago
     * Cliente secret de Mercado Pago
     *   USUARIO 1 VENDEDOR
     *   {"id":261381943,"nickname":"TETE1997307","password":"qatest1300","site_status":"active","email":"test_user_71038684@testuser.com"}
     *   USUARIO 2 COMPRADOR
     *   {"id":261388199,"nickname":"TETE2126547","password":"qatest7061","site_status":"active","email":"test_user_98717172@testuser.com"}
     */
    private $client_id_sandbox = '7138922892231670';
    private $client_secret_sandbox = 'geibucLUNVeQ9BviNaCjgiAFSiluDqvB';    
    
    /**
     * Funciona en mode de prueba
     * @var string
     */
    private $sandbox = false;    
    
    private $dominioEntorno = '';
    
    /**
     * Mercado Pago object
     */
    private $mp = null;

    
    public function __construct(ContainerInterface $container) 
    {
        $this->container = $container;

        // si existe paremetro sandbox en parameters inicializo api con su valor
        if ($this->container->hasParameter('sandbox')) {
            $this->sandbox = $this->container->getParameter('sandbox');
        }
        
        $cid = $this->client_id;
        $csecret  = $this->client_secret;          
        $this->dominioEntorno = 'https://sysipt.com.ar';
        
        // si es sandbox utilizo el usuario de prueba
        if ($this->sandbox) {
            $cid = $this->client_id_sandbox;
            $csecret  = $this->client_secret_sandbox;
            $this->dominioEntorno = $this->dominioEntorno.'/app_dev.php';
        }
        
        // inicializo api mercado pago
        $this->mp = new MercadoPago\MP($cid, $csecret);          
        $this->mp->sandbox_mode($this->sandbox);
    }
    
    public function setSandbox($sandbox = null)
    {
        if ($sandbox) {
            $this->sandbox($sandbox);
            $this->mp->sandbox_mode($sandbox);            
        }
    }
    
    public function getToken()
    {
        return $this->mp->get_access_token();
    }
    
    public function crearPreferencia($cupon)
    {             
        $preference = $this->mp->create_preference($this->getValoresParaPreferenciaFromCupon($cupon));                     
        
        return $preference['response'];        
    }    
    
    public function getPreferencia($id)
    {
        return $this->mp->get_preference($id);
    }
    
    public function modificarPreferencia($id,$cupon)
    {
        return $this->mp->update_preference($id, $this->getValoresParaPreferenciaFromCupon($cupon));
    }    
    
    public function getMp()
    {
        return $this->mp;
    }
    
    public function getCredenciales() {
        
        if (!$this->sandbox) {
            $credenciales = $this->client_id.' - '.$this->client_secret;
        } else {
           $credenciales = $this->client_id_sandbox.' - '.$this->client_secret_sandbox; 
        }
        
        return $credenciales;
    }
    
    private function getValoresParaPreferenciaFromCupon($cupon) {

        $items = array();
        $title = "";
        $montoTotal = 0;
        $montoEnvio = 0;
        
        foreach ($cupon->getItems() as $index => $item) {            
            
            if ($index>0) {
                $title.=" + ";
            }
            
            $title .= $item->getCantidad()." ";
            $title .= utf8_encode($item->getNombre());    
            
            $montoEnvio += $item->getMontoEnvio();
            $montoTotal += (float)$item->getMontoTotal();
        }
        
        if ($montoEnvio > 0) {
            $title .= " + costo de envío";
        }
        
        $items[0] = array(
            "id" => $cupon->getId(),
            "title" => $title,
            "category_id" => "television", //https://api.mercadopago.com/item_categories
            "quantity" => 1,
            "currency_id" => "ARS",
            "unit_price" => $montoTotal,
        );
        
        $payer = array(
            "email" => $cupon->getEmail(),
            'phone' => array(
                'area_code' => '380',
                'number'    => $cupon->getTelefono(),
            ),
            'address' => array(
                'street_name'   => $cupon->getDireccionCalle(),
                'street_number' => $cupon->getDireccionNumero(),
                'zip_code'      => $cupon->getDireccionCp(),
            ),    
        );
                
//        $payment_methods = array( 
//            "excluded_payment_types" => 
//               array( 
//                   //array("id"=>"ticket"),  //https://api.mercadolibre.com/sites/MLA/payment_methods
//            ),
////            "installments" => 10,
////            "installments" => 1,
//        );
        
        $fechaValidaDesde = new \DateTime();
        $fechaValidaHasta = new \DateTime();
        $fechaValidaHasta->add(new \DateInterval('P365D'));
        
        $datosPreferencia = array(
            "items" => $items,
            "payer" => $payer,
//            "payment_methods" => $payment_methods,
            "notification_url" => $this->dominioEntorno.'/mp/notificar/sfhiweufrhiwegrwerb/axcewwfgy-la-le-r2-2',  
            "external_reference" => $cupon->getId(),   
            "expires" => false,
            "expiration_date_from" => null,
            "expiration_date_to" => null,            
        );  
        
        return $datosPreferencia;
    }
}