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
    { value: 'WIFI06', label: 'WIFI 6MB' },
    { value: 'WIFI10', label: 'WIFI 10MB' },
    { value: 'WIFI20', label: 'WIFI 20MB' },
    { value: 'TV', label: 'TV' },
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
      conexiones: [],
    };
  }

  componentWillReceiveProps(nextProps)
  {

    // si se marco una nueva zona para pintar
    if(
      nextProps.drawingManagerPolygonComplete!=null
      && this.props.drawingManagerPolygonComplete!=nextProps.drawingManagerPolygonComplete
      && nextProps.consumidorHerramientaMapaActual=="filtro-conexiones"
    ) {

      // evito que se ejecuten varios fitros en paralelo
      if (!this.ejecutandoFiltro) {

        this.offset = 0
        this.getConexiones(nextProps.drawingManagerPolygonComplete)

      } else {

        nextProps.herramientaConexiones.drawing.setMap(null)
        alert("Actualmente se están cargando conexiones. Espere a que el proceso termine y vuelva a intentarlo")
      }
    }
  }

  getConexiones(drawing)
  {
    if (!this.ejecutandoFiltro) {

      let servicios = this.state.selectedOptionServicio != null ? this.state.selectedOptionServicio.map((e) => e.value) : [];
      let estados = this.state.selectedOptionEstado != null ? this.state.selectedOptionEstado.map((e) => e.value) : [];
      let poligono = drawing.getPath().getArray() != null ? drawing.getPath().getArray().map((e) => {return e.lat()+","+e.lng()}) : [];

      this.urlBaseFetch = this.props.scheme+'://'+this.props.host+this.props.baseUrl+"/"+this.props.usuarioHash+"/red/conexion/filtro?s="+servicios.join(";")+"&e="+estados.join(";")+"&p="+poligono.join(";")
      this.ejecutandoFiltro = true
    }

    fetch(this.urlBaseFetch + "&offset=" + this.offset)
    .then(res => res.json())
    .then(data => {

      let m = this.props.markers

      if (data.seguirCargando) {

        if (data.conexiones.length > 0) {

          /*c = c.concat(data.conexiones);
          c = Array.from(new Set(c.map(JSON.stringify))).map(JSON.parse);*/

          let p = m.concat(data.conexiones.map((e) => {

            return  {
              "id":"conexiones-" + e.id,
              "visible": 1,
              "lat":parseFloat(e.latitud),
              "lng":parseFloat(e.longitud),
              "svgPath":'M0,0h32v32H0V0z',
              "color":"#018BFE",
              "opacity":1,
              "title":"id conexión: " + e.id.toString(),
              "minZoom": 0,
              "elemento":e,
              "tipoMarker":"herramienta-conexiones"
            }

          }));

          let promise = this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='herramienta-conexiones'))
          promise.then((success) => {

            let m = this.props.markers.concat(p)
            m = Array.from(new Set(m.map(JSON.stringify))).map(JSON.parse);
            this.props.setMarkers(m)

            this.offset++;
            this.getConexiones(drawing);

          })

        } else {

          this.offset++;
          this.getConexiones(drawing);

        }

      } else {

        this.ejecutandoFiltro = false
        this.urlBaseFetch = ''
        this.offset = 0

        drawing.setMap(null)
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
      this.props.handleConsumidorHerramientaMapaActual("filtro-conexiones")
      this.props.handleHerramientaMapaActual(["drawingManagerPolygon"]);
    } else {
      this.props.handleConsumidorHerramientaMapaActual(null)
      this.props.handleHerramientaMapaActual([]);
    }
  }

  quitarConexiones = () => {

    if (!this.ejecutandoFiltro) {

      this.setState({"conexiones":[]})
      this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='herramienta-conexiones'))

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
