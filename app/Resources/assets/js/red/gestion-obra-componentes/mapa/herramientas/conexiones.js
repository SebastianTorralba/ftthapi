import 'babel-polyfill';

import React, {Component} from 'react';
import { Button, Popover, PopoverHeader, PopoverBody, ButtonGroup } from 'reactstrap';
import {styles} from '../opciones.json'
import Select from 'react-select';

export class Conexiones extends Component {

  ejecutandoFiltro = false
  urlBaseFetch = ''
  offset = 0
  drawing = null
  cantidadConexiones = []

  tiposServicios = [
    { value: 'HFC06', label: 'HFC 6MB' },
    { value: 'HFC12', label: 'HFC 12MB' },
    { value: 'HFC20', label: 'HFC 20MB' },
    { value: 'HFC30', label: 'HFC 30MB' },
    { value: 'HFC40', label: 'HFC 40MB' },
    { value: 'HFC50', label: 'HFC 50MB' },
    { value: 'FTTH50', label: 'FTTH 50MB' },
    { value: 'FTTH100', label: 'FTTH 100MB' },
    { value: 'TV', label: 'TV' },
    { value: 'WIFI06', label: 'WIFI 6MB' },
    { value: 'WIFI10', label: 'WIFI 10MB' },
    { value: 'WIFI20', label: 'WIFI 20MB' },
  ]

  tiposEstados = [
    { value: 3, label: 'Conectado' },
    { value: 1, label: 'Pendiente' },
    { value: 9, label: 'Zona no cableada' },
    { value: 6, label: 'Suspendido' },
    { value: 5, label: 'Cortado' },
    { value: 61, label: 'Gest. corte (3 facturas impagas)' },
    { value: 62, label: 'Gest. corte' },
  ]

  constructor(props) {
    super(props);

    this.state = {
      popoverOpen: false,
      activarSelected: 'no',
      selectedOptionServicio: null,
      selectedOptionEstado: null,
    };
  }

  componentWillReceiveProps(nextProps)
  {

    // si se marco una nueva zona para pintar
    if (nextProps.herramientaConexiones.drawing != null &&
          this.props.herramientaConexiones.drawing !== nextProps.herramientaConexiones.drawing) {

      // evito que se ejecuten varios fitros en paralelo
      if (!this.ejecutandoFiltro) {
        this.offset = 0
        this.getConexiones(nextProps.herramientaConexiones.drawing, nextProps.herramientaConexiones.puntos)

      } else {

        nextProps.herramientaConexiones.drawing.setMap(null)
        alert("Actualmente se están cargando conexiones. Espere a que el proceso termine y vuelva a intentarlo")
      }
    }
  }

  getConexiones(drawing, puntos)
  {

    if (!this.ejecutandoFiltro) {

      let servicios = this.state.selectedOptionServicio != null ? this.state.selectedOptionServicio.map((e) => e.value) : [];
      let estados = this.state.selectedOptionEstado != null ? this.state.selectedOptionEstado.map((e) => e.value) : [];
      let poligono = drawing.getPath().getArray() != null ? drawing.getPath().getArray().map((e) => {return e.lat()+","+e.lng()}) : [];

      this.urlBaseFetch = 'http://'+this.props.host+this.props.baseUrl+"/"+this.props.usuarioHash+"/red/conexion/filtro?s="+servicios.join(";")+"&e="+estados.join(";")+"&p="+poligono.join(";")
      this.drawing = drawing
      this.ejecutandoFiltro = true
    }

    fetch(this.urlBaseFetch + "&offset=" + this.offset)
    .then(res => res.json())
    .then(data => {

      let p = puntos

      if (data.seguirCargando) {

        if (data.conexiones.length > 0) {
          p = p.concat(data.conexiones);
          p = Array.from(new Set(p.map(JSON.stringify))).map(JSON.parse);

          this.props.handleHerramientaConexiones({
            "puntos": p,
            "drawing": this.drawing,
            "cantidad": this.getCantidad(p)
          })
        }

        this.offset++;
        this.getConexiones(this.drawing, p);

      } else {

        this.drawing.setMap(null)
        this.ejecutandoFiltro = false
        this.urlBaseFetch = ''
        this.offset = 0
        this.drawing = null
      }

    })

  }

  handleChangeServicio = (selectedOptionServicio) => {
    this.setState({ selectedOptionServicio });
  }

  handleChangeEstado = (selectedOptionEstado) => {
    this.setState({ selectedOptionEstado });
  }

  togglePopover = () => {
    this.setState({
      popoverOpen: !this.state.popoverOpen
    });
  }

  onRadioBtnActivarClick = (activarSelected) => {

    this.setState({ activarSelected });

    if (activarSelected == "si") {
      this.props.handleHerramientaMapaActual("filtro-conexiones")
    } else {
      this.props.handleHerramientaMapaActual(null)
    }
  }

  quitarConexiones = () => {

    if (!this.ejecutandoFiltro) {
      this.props.handleHerramientaConexiones({
        "puntos": [],
        "drawing": this.drawing,
        "cantidad": []
      })
    } else {
      alert("Actualmente se están cargando conexiones. Espere a que el proceso termine y vuelva a intentarlo")
    }
  }

  getCantidad(conexiones)
  {
    let cantidad = []

    if (Array.isArray(conexiones) && conexiones.length > 0) {

      cantidad.push({
        "nombre":"Conexiones",
        "total":conexiones.length
      })

      let strTodaLasTarifas = conexiones.map((c) => c.tarifas).join(" - ")


      this.tiposServicios.map((tipo) => {

        let matchTipo = strTodaLasTarifas.match(new RegExp(tipo.value,"g"))

        if (matchTipo != null) {

          cantidad.push({
            "nombre": tipo.label,
            "total": matchTipo.length
          })
        }
      })

    }

    return cantidad;
  }

  render() {
    return (
      <span>
        <Button className="mr-1" color={this.state.activarSelected == "si" ? "primary" : "light"} id={'popover-herramienta-conexiones'} onClick={this.togglePopover}>
          <i className="fa fa-user-o" aria-hidden="true"></i>
        </Button>
        <Popover className="menuPopover" placement={'right'} isOpen={this.state.popoverOpen} target={'popover-herramienta-conexiones'} toggle={this.togglePopover}>
          <PopoverHeader>Herramienta conexiones</PopoverHeader>
          <PopoverBody style={{margin: "5px", width:"400px"}} >

            <div className="form-group" >
              <ButtonGroup>
                <Button onClick={() => this.onRadioBtnActivarClick('si')} active={this.state.activarSelected === 'si'} size="sm" color="primary">Activar</Button>
                <Button onClick={() => this.onRadioBtnActivarClick('no')} active={this.state.activarSelected === 'no'} size="sm" color="primary">Desactivar</Button>
              </ButtonGroup>
              <a href="javascript:void(0)" onClick={() => this.quitarConexiones()} className="pull-right" style={{color:'#333',paddingTop:'5px'}}>Quitar conexiones del mapa</a>
            </div>

            <div className="form-group" >

              <Select
                placeholder={'Filtrar por tipo de servicio'}
                isMulti={true}
                value={this.state.selectedOptionServicio}
                onChange={this.handleChangeServicio}
                options={this.tiposServicios}
              />
            </div>

            <div className="form-group" >
              <Select
                placeholder={'Filtrar por estado conexión'}
                isMulti={true}
                value={this.state.selectedOptionEstado}
                onChange={this.handleChangeEstado}
                options={this.tiposEstados}
              />
            </div>

          </PopoverBody>
        </Popover>
      </span>
    );
  }
}
