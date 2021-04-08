<?php

namespace AppBundle\Service;

use \Symfony\Component\DependencyInjection\ContainerInterface;

class Paginador
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * Nombre de la variable querystring que se utiliza para especificar el número de página
     * @var string
     */
    private $paginaQueryVar = 'np';
    
    /**
     * Cantidad de registros por página a mostrar en los timelines
     * @var integer
     */
    private $registrosPorPagina = 5;

    /**
     * Cantidad total de registros
     * @var integer
     */
    private $totalRegistros;

    /**
     * Render vista paginador
     * @var string
     */
    private $render;

    public function __construct(ContainerInterface $container) 
    {
        $this->container = $container;
    }

    /**
     * Páginación: << anterior - actual - siguiente >> (Y timelines)
     *
     * Calcula, valida y devuelve paginaActual, paginaAnterior y paginaSiguiente
     *
     * @param integer $totalRegistros Cantidad total de registros para la consulta a paginar
     * @return array Array conteniendo paginaActual, paginaAnterior y paginaSiguiente
     *
     * @deprecated deprecado desde aplicación rama administración, usar setTotalRegistros(), luego getPaginasTimeline()
     */
    public function getPaginas($totalRegistros)
    {
        $this->totalRegistros = $totalRegistros;

        return $this->getPaginasTimeline();
    }

    /**
     * Páginación Timelines: << anterior - actual - siguiente >>
     *
     * Calcula, valida y devuelve paginaActual, paginaAnterior y paginaSiguiente
     *
     * @param integer $totalRegistros Cantidad total de registros para la consulta a paginar
     * @return array Array conteniendo paginaActual, paginaAnterior y paginaSiguiente
     */
    public function getPaginasTimeline()
    {
        $paginaActual = $this->getPaginaActual();
        $paginaUltima = $this->getPaginaUltima($this->totalRegistros);

        if ($paginaActual < 1) {
            $paginaActual = 1;
        } else if ($paginaActual > $paginaUltima) {
            $paginaActual = $paginaUltima;
        }

        $paginaAnterior = $paginaActual > 1 ? $paginaActual - 1 : false;
        $paginaSiguiente = $paginaActual < $paginaUltima ? $paginaActual + 1 : false;

        return compact('paginaActual', 'paginaAnterior', 'paginaSiguiente');
    }

    public function getRegistrosPorPagina()
    {
        return $this->registrosPorPagina;
    }

    public function getTotalRegistros()
    {
        return $this->totalRegistros;
    }

    public function getPaginaActual()
    {
        $valor = (int) $this->container->get('request_stack')->getCurrentRequest()->get($this->paginaQueryVar);
        
        return  $valor ? $valor : 1;
    }

    public function getPaginaUltima()
    {
        return $this->getTotalPaginas();
    }

    public function getTotalPaginas()
    {
        return (int) ceil($this->getTotalRegistros() / $this->registrosPorPagina);
    }

    public function getOffset()
    {
        return $this->getPaginaActual() == 1 ? 0 : ($this->getPaginaActual()-1) * $this->getRegistrosPorPagina();
    }

    public function setRegistrosPorPagina($cantidad)
    {
        $this->registrosPorPagina = $cantidad;

        return $this;
    }

    public function setTotalRegistros($total)
    {
        $this->totalRegistros = $total;

        return $this;
    }

    /**
     * GB: Esto es algo que tengo desde hace mucho tiempo, lo adapte, no tenía tiempo de hacerlo más lindo
     *
     * Renderiza paginación tradicional
     *
     * @return string   HTML de páginación
     */
    public function render($mostrarCantidades = true)
    {
        if($this->render)
            return $this->render;

        // no hace falta paginación
        if($this->getTotalRegistros() <= $this->getRegistrosPorPagina())
            return '';

        // tomo la url y el querystring sin el page=x
        $url = explode('?', $_SERVER['REQUEST_URI']);
        $base_url	= '//' . $_SERVER['HTTP_HOST'] . $url[0];
        $query		= isset($url[1])
            ? preg_replace('/('.$this->paginaQueryVar.'=[0-9]{0,}&|'.$this->paginaQueryVar.'=[0-9]{0,})/', '', $url[1])
            : ''
        ;

        $new_url_no_page = $base_url . (!empty($query) ? '?'.$query : '');

        // nueva url
        $new_url	=  $base_url . (!empty($query) ? '?'.$query.'&'.$this->paginaQueryVar.'=' : '?'.$this->paginaQueryVar.'=');
        $new_url	= preg_replace('/([&&]{2,})/', '&', $new_url);

        // cantidad de paginas
        $pages = $this->getTotalPaginas();

        // configuro cuantos links de paginas muestro
        //$num_links = ($this->getPaginaActual() < 50 ? 10 : 8);
        $num_links = 10;

        $link_primero	= false;
        $link_ultimo	= false;

        if($pages <= $num_links)
        {
            $p_start = 1;
            $p_break = $pages;
        }else{
            if($this->getPaginaActual() <= ($num_links/2))
            {
                $p_start=1;
                $p_break = $num_links;
                $link_ultimo = true;
            }else{
                $p_start = ($this->getPaginaActual() - ($num_links/2));
                $p_break = $this->getPaginaActual() + ($num_links/2);
                if($p_break > $pages) $p_break = $pages;
                $link_primero = ($p_start==1)? false : true;
                $link_ultimo = ($p_break==$pages)? false : true;
            }
        }

        // armo la paginación
        $this->render = '<div class="pagination pagination-small pagination-right margin-b-small"><ul>';

        if($link_primero)
            $this->render .= '<li><a href="'. $new_url_no_page . '">&laquo; &laquo;</a></li> ';

        // anterior
        if($this->getPaginaActual() > 1)
        {
            $prev = ($this->getPaginaActual() - 1);

            $this->render .= '<li><a href="'. ($prev==1 ? $new_url_no_page : $new_url . $prev ).'">&laquo;</a></li> ';
        }else{
            $this->render .= '<li class="disabled"><a href="#">&laquo;</a></li> ';
        }

        // paginas
        for($p=$p_start; $p <= $pages; $p++)
        {
            if($this->getPaginaActual() == $p)
            {
                $this->render .= '<li class="active"><a href="#">'. $p .'</a></li> ';
            }else{
                $this->render .= '<li><a href="'.($p==1 ? $new_url_no_page : $new_url . $p ).'">'. $p .'</a></li> ';
            }

            if($p == $p_break) break;
        }

        // siguiente
        if($this->getPaginaActual() < $pages)
        {
            $this->render .= '<li><a href="'. $new_url . ($this->getPaginaActual() + 1) .'">&raquo;</a></li> ';
        }else{
            $this->render .= '<li class="disabled"><a href="#">&raquo;</a></li> ';
        }

        // última
        if($link_ultimo)
            $this->render .= '<li><a href="'. $new_url . $pages .'">&raquo; &raquo;</a></li>';

        // cierre ul
        $this->render .= '</ul>';

        if($mostrarCantidades == true) {

            $trans = $this->container->get('translator');



            $this->render = $this->render . ' ' .'<br><small class="muted">'. $trans->trans('comun.paginador.viendo',
                array(
                    '%del%'   => $this->getOffset()+1,
                    '%al%'    => ($cant = $this->getPaginaActual()*$this->getRegistrosPorPagina()) < $this->getTotalRegistros() ? $cant : $this->getTotalRegistros(),
                    '%de%'    => $this->getTotalRegistros()
                )
            ).'</small>';
        }

        //cierre div
        $this->render .= '</div>';

        return $this->render;
    }
    
    /**
     * SF: Basado en el render de GB
     *
     * Renderiza paginación tradicional con formato para Twitter Bootstrap 3
     *
     * @return string   HTML de páginación
     */    
    public function renderBootstrap3()
    {
        // no hace falta paginación
        if($this->getTotalRegistros() <= $this->getRegistrosPorPagina())
            return '';

        // tomo la url y el querystring sin el page=x
        $url = explode('?', $_SERVER['REQUEST_URI']);
        $base_url	= '//' . $_SERVER['HTTP_HOST'] . $url[0];
        $query		= isset($url[1])
            ? preg_replace('/('.$this->paginaQueryVar.'=[0-9]{0,}&|'.$this->paginaQueryVar.'=[0-9]{0,})/', '', $url[1])
            : ''
        ;

        $new_url_no_page = $base_url . (!empty($query) ? '?'.$query : '');

        // nueva url
        $new_url	=  $base_url . (!empty($query) ? '?'.$query.'&'.$this->paginaQueryVar.'=' : '?'.$this->paginaQueryVar.'=');
        $new_url	= preg_replace('/([&&]{2,})/', '&', $new_url);

        // cantidad de paginas
        $pages = $this->getTotalPaginas();


        // configuro cuantos links de paginas muestro
        //$num_links = ($this->getPaginaActual() < 50 ? 10 : 8);
        $num_links = 10;

        $link_primero	= false;
        $link_ultimo	= false;

        if($pages <= $num_links)
        {
            $p_start = 1;
            $p_break = $pages;
        }else{
            if($this->getPaginaActual() <= ($num_links/2))
            {
                $p_start=1;
                $p_break = $num_links;
                $link_ultimo = true;
            }else{
                $p_start = ($this->getPaginaActual() - ($num_links/2));
                $p_break = $this->getPaginaActual() + ($num_links/2);
                if($p_break > $pages) $p_break = $pages;
                $link_primero = ($p_start==1)? false : true;
                $link_ultimo = ($p_break==$pages)? false : true;
            }
        }

        // armo la paginación
        $str_pages = '<ul class="pagination pull-right">';

        if($link_primero)
            $str_pages .= '<li><a href="'. $new_url_no_page . '">&laquo; &laquo;</a></li> ';

        // anterior
        if($this->getPaginaActual() > 1)
        {
            $prev = ($this->getPaginaActual() - 1);

            $str_pages .= '<li><a href="'. ($prev==1 ? $new_url_no_page : $new_url . $prev ).'">&laquo;</a></li> ';
        }else{
            $str_pages .= '<li class="disabled"><a href="#">&laquo;</a></li> ';
        }

        // paginas
        for($p=$p_start; $p <= $pages; $p++)
        {
            if($this->getPaginaActual() == $p)
            {
                $str_pages .= '<li class="active"><a href="#">'. $p .'</a></li> ';
            }else{
                $str_pages .= '<li><a href="'.($p==1 ? $new_url_no_page : $new_url . $p ).'">'. $p .'</a></li> ';
            }

            if($p == $p_break) break;
        }

        // siguiente
        if($this->getPaginaActual() < $pages)
        {
            $str_pages .= '<li><a href="'. $new_url . ($this->getPaginaActual() + 1) .'">&raquo;</a></li> ';
        }else{
            $str_pages .= '<li class="disabled"><a href="#">&raquo;</a></li> ';
        }

        // última
        if($link_ultimo)
            $str_pages .= '<li><a href="'. $new_url . $pages .'">&raquo; &raquo;</a></li>';

        // cierre
        $str_pages .= '</ul>';

        return $str_pages;
    }    
}