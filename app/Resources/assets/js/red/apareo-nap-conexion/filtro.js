import React, {Component} from 'react';
import {Menu} from './window/menu';
import {Body} from './window/body';

import { Button, Popover, PopoverHeader, PopoverBody, ButtonGroup } from 'reactstrap';
import Select from 'react-select';

export class Filtro extends Component {

  tiposServicios = [
    { value: '139', label: 'FTTH 50MB' },
    { value: '133', label: 'FTTH 100MB' },
    { value: '144', label: 'FTTH 50MB WIFI' },
    { value: '143', label: 'FTTH 100MB WIFI' },
    { value: '155', label: 'FTTH 100MB WIFI' },
    { value: '156', label: 'FTTH 200MB WIFI' },
    { value: '157', label: 'FTTH 300MB WIFI' },
    { value: '158', label: 'FTTH 300MB WIFI' },

  ];

  tiposEstados = [
    { value: 3, label: 'Conectado' },
    { value: 1, label: 'Pendiente' },
  ];

  tiposOperacion = [
    { value: 'cambiotarifa', label: 'Cambio de tarifa' },
  ];

  constructor(props) {
    super(props)

    this.state = {
      "popoverOpen": false,
      "activarFiltro": 'no',
      "selectedOptionServicio": null,
      "selectedOptionEstado": null,
      "selectedOptionOperacion": null,
      "selectedOptionConexion": null,
      "conexionesPendientes": [],
      "load": 0,
    }
  }

  componentWillMount() {
    // inicializo elementos de red en el mapa
  //  this.getConexionesPendientes()
  }

  getConexionesPendientes = () => {

    // armo valores del filtro
    let servicios = this.state.selectedOptionServicio != null ? this.state.selectedOptionServicio.map((e) => e.value) : [];
    let estados = this.state.selectedOptionEstado != null ? this.state.selectedOptionEstado.map((e) => e.value) : [];
    let operaciones = this.state.selectedOptionOperacion != null ? this.state.selectedOptionOperacion.map((e) => e.value) : [];
    let cnx = this.state.selectedOptionConexion != null ? this.state.selectedOptionConexion : '';

    let url = this.props.uri + "conexiones-pendientes?s="+servicios.join(";")+"&e="+estados.join(";")+"&o="+operaciones.join(";")+"&c="+cnx
    this.ejecutandoFiltro = true

    fetch(url)
    .then(res => res.json())
    .then(data => {

      this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='conexion-ftth'))

      this.setState({
        "conexionesPendientes":data.elementos
      }, () => {
        this.setMarkers()
      })

    })
  }

  setMarkers() {

    let p = this.state.conexionesPendientes.map((e) => {
        return {
          "id":e.id,
          "visible": 1,
          "lat":e.latitud,
          "lng":e.longitud,
          "opacity":1,
          "title": "CNX " + e.id.toString(),
          "minZoom": 0,
          "tipo":"conexion",
          "elemento":e,
          "tipoMarker":"conexion-ftth"
        }
    });

    let promise = this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='conexion-ftth' && e.tipoMarker!='conexion-ftth'))
    promise.then((success) => {
      let m = this.props.markers.concat(p)
      m = Array.from(new Set(m.map(JSON.stringify))).map(JSON.parse);
      this.props.setMarkers(m)
    })

  }

  togglePopover = () => {
    console.log(this.state.popoverOpen)
    this.setState({
      popoverOpen: !this.state.popoverOpen
    });
  }

  buscar = () => {
    this.togglePopover();
    this.getConexionesPendientes();
    this.state.activarFiltro = "si";
  }

  quitarConexiones = () => {

    this.togglePopover();
    this.state.activarFiltro = "no";

    this.setState({
      "conexionesPendientes": []
    }, () => {
      this.setMarkers()
    })
  }

  render() {
    return (
      <div className="mapa-filtro">

        <Button className="mr-1" color={this.state.activarFiltro == "si" ? "primary" : "light"} id={'popover-filtro-conexiones'} onClick={this.togglePopover}  >
          <i className="fa fa-filter" aria-hidden="true"></i>
        </Button>

        <Popover className="menuPopover" placement={'right'} isOpen={this.state.popoverOpen} target={'popover-filtro-conexiones'} toggle={this.togglePopover}>
          <PopoverHeader>CNX FTTH pendientes para asociar NAP</PopoverHeader>
          <PopoverBody style={{margin: "5px", width:"400px"}} >

            <div className="form-group" >
              <Button onClick={ () => { this.buscar() } } size="sm" color="primary">Buscar</Button>
              <a href="javascript:void(0)" onClick={() => this.quitarConexiones()} className="pull-right" style={{color:'#333',paddingTop:'5px'}}>Quitar conexiones del mapa</a>
            </div>

            <div className="form-group" >
              <input
                className="form-control"
                placeholder={"Id conexión"}
                value={this.state.selectedOptionConexion}
                onChange={(e) => { this.setState({ selectedOptionConexion: e.target.value }) }}
              />
            </div>

            <div className="form-group" >
              <Select
                placeholder={'Tipo de servicio'}
                isMulti={true}
                value={this.state.selectedOptionServicio}
                onChange = {(selectedOptionServicio) => { this.setState({ selectedOptionServicio }) }}
                options={this.tiposServicios}
              />
            </div>

            <div className="form-group" >
              <Select
                placeholder={'Estado actual cnx'}
                isMulti={true}
                value={this.state.selectedOptionEstado}
                onChange = {(selectedOptionEstado) => { this.setState({ selectedOptionEstado }) }}
                options={this.tiposEstados}
              />
            </div>

            <div className="form-group" >
              <Select
                placeholder={'Tipo operación'}
                isMulti={true}
                value={this.state.selectedOptionOperacion}
                onChange = {(selectedOptionOperacion) => { this.setState({ selectedOptionOperacion }) }}
                options={this.tiposOperacion}
              />
            </div>

          </PopoverBody>
        </Popover>


      </div>
    )

  }
}
