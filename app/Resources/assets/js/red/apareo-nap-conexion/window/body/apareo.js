import React, {Component} from 'react';
require('es6-promise').polyfill();
require('isomorphic-fetch');
const objectAssign = require('object-assign');
import Select from 'react-select';


export class Apareo extends Component {

  inside = require('point-in-polygon')
  linkElement = null
  apareoNuevoConexion = 0
  apareoNuevoElemento = 0

  constructor(props) {
    super(props)

    this.state = {
      "apareos": [],
      "elementosRed": [],
      "apareoNuevoConexion": 0,
      "apareoNuevoElemento": 0,
      "saving": 0,
      "load": 0,
      "saving":false
    }
  }

  componentWillMount() {
    // inicializo elementos de red en el mapa
    this.getElementosRed()
    //this.getConexionesPendientes()
  }

  componentWillReceiveProps = (nextProps) => {

    // para agregar o quitar elementos de red a la obra por click
    if(nextProps.markerClick!=null
      && this.props.markerClick!=nextProps.markerClick){

      this.gestionarApareoPorClick(nextProps.markerClick.marker);
    }
  }

  gestionarApareoPorClick(el) {

    if (el.tipoMarker=="conexion-ftth") {

      let apareoValidarConexion = this.state.apareos.filter(apa => apa.conexion.id==el.id)

      if (apareoValidarConexion.length == 0) {

        this.setState({
          "apareoNuevoConexion": el
        }, () => {
          this.procesarApareo()
        })

      } else {
        alert("La conexión " + el.id + " ya fue asociada con el NAP " + apareoValidarConexion[0].elementoRed.elemento.codigo)
      }
    }

    if (el.tipoMarker=="elementos-red") {

      this.setState({
        "apareoNuevoElemento": el
      }, () => {
        this.procesarApareo()
      })
    }
  }

  procesarApareo() {

    if (this.state.apareoNuevoConexion != 0 && this.state.apareoNuevoElemento != 0) {

      let apa = this.state.apareos
      apa.push({conexion:this.state.apareoNuevoConexion, elementoRed:this.state.apareoNuevoElemento})

      this.setState({
        "apareos": apa,
        "apareoNuevoConexion":0,
        "apareoNuevoElemento":0
      })

    }
  }

  getElementosRed = () => {

    let url = this.props.uri + "elementos-red/"
    fetch(url)
    .then(res => res.json())
    .then(data => {

      this.setState({
        "elementosRed":data.elementos
      }, () => {
        this.setMarkers()
      })

    })
  }

  /*getConexionesPendientes = () => {

    let url = this.props.uri + "conexiones-pendientes/"
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
  }*/

  setMarkers() {

    let p = this.state.elementosRed.filter((e) => {
      return this.state.id!=e.id && e.elementoTipo.tipoGeoreferencia == "punto" && e.georeferencias && e.georeferencias.length > 0;
    }).map((e) => {

      return {
        "id":e.id,
        "visible": 1,
        "lat":parseFloat(e.georeferencias[0].latitud),
        "lng":parseFloat(e.georeferencias[0].longitud),
        "image": '/uploads/imagenes/tipo-elemento/'+e.elementoTipo.id+"_"+e.estadoActual.estado+"_"+this.props.dimensionMarker+".png",
        "opacity":1,
        "obra_id":0,
        "title":e.codigo+" - "+e.nombre+" - "+e.estadoActual.estado,
        "minZoom": 0,
        "tipoMarker":"elemento-red",
        "elemento": {
          ...e,
          ...
            {"resumen":
              {"categoria":"Red",
               "elementos": [{
                  "id":e.elementoTipo.id,
                  "descripcion":e.elementoTipo.nombre,
                  "estado":e.estadoActual.estado,
                  "cantidad":1,
                  "unidad":""
                }]
              }
            }
        },
        "tipoMarker":"elementos-red"
      }

    });

    let promise = this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='elementos-red' && e.tipoMarker!='elemento-red'))
    promise.then((success) => {
      let m = this.props.markers.concat(p)
      m = Array.from(new Set(m.map(JSON.stringify))).map(JSON.parse);
      this.props.setMarkers(m)
    })

  }

  removeMarkersApareados() {
    let idsRemove = this.state.apareos.map(e => e.conexion.id)
    this.props.setMarkers(this.props.markers.filter((e) => !(e.tipoMarker == 'conexion-ftth' && idsRemove.indexOf(e.id) >= 0)))
  }

  quitar(apareo) {

    let apareoQuitarConexion = this.state.apareos.filter(apa => apa.conexion.id != apareo.conexion.id && apa.elementoRed.elemento.codigo != apareo.elementoRed.elemento.codigo)

    this.setState({
      "apareos": apareoQuitarConexion
    })

  }

  quitarNuevo() {

    this.setState({
      "apareoNuevoConexion":0,
      "apareoNuevoElemento":0
    })

  }

  save() {

    if (this.state.apareoNuevoConexion != 0 || this.state.apareoNuevoElemento != 0) {
      alert("ERROR! Asociación en curso incompleta. Finalice o cancele la asociaciòn pendiente y vuelva a intentar")
      return
    }

    if (this.state.apareos.length == 0) {
      alert("ERROR! No existen asociaciones para enviar")
      return
    }

    this.setState({
      "saving":true,
    })

    let url = this.props.uri + "save/"
    fetch(url, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        "apareos": this.state.apareos.map((apa) => { return {conexion:apa.conexion.id, elementoRed:apa.elementoRed.elemento.id}}),
    })})
    .then(res => res.json())
    .then(data => {

      if (data.resultado == "ok") {

        this.removeMarkersApareados();

        this.setState({
          "apareos":[],
          "apareoNuevoConexion":0,
          "apareoNuevoElemento":0,
          "saving": false,
        }, () => {
          this.removeMarkersApareados()
        })

        alert("Cambios guardados con éxito")

      } else {

        this.setState({
          "apareoNuevoConexion":0,
          "apareoNuevoElemento":0,
          "saving": false,
        })
        alert("ERROR! No se guardaron los cambios")
      }

    })
  }

  _renderSeleccionActual(){
      return (
        this.state.apareoNuevoConexion != 0 || this.state.apareoNuevoElemento!=0 || this.state.apareos.length > 0 ?
          <div>

            <h6 style={{padding:"3px", backgroundColor:'#fff'}} colspan="2" scope="col" className="text-center">
              Asociasiones para agregar
            </h6>
            <table className="table">
              <thead  className="thead-dark" >
                <tr>
                  <th style={{padding:"3px"}} scope="col" className="text-center">Conexión</th>
                  <th style={{padding:"3px"}} scope="col" className="text-center">CTO</th>
                  <th style={{padding:"3px"}} scope="col" className="text-center"></th>
                </tr>
              </thead>
              <tbody>
                {this.state.apareoNuevoConexion != 0 || this.state.apareoNuevoElemento!=0 ?
                  <tr>
                    <td style={{padding:"5px"}} width={"45%"} className="text-center">
                      {this.state.apareoNuevoConexion != 0 ? this.state.apareoNuevoConexion.elemento.id : ''}
                    </td>
                    <td style={{padding:"5px"}} width={"45%"} className="text-center">
                      {this.state.apareoNuevoElemento != 0 ? this.state.apareoNuevoElemento.elemento.codigo : ''}
                    </td>
                    <td style={{padding:"5px"}} width={"10%"} className="text-center">
                      <button type="button" onClick={(e) => { this.quitarNuevo() }}>X</button>
                    </td>
                  </tr>
                  :
                  null
                }
                {this.state.apareos.map((apareo, index) => {
                  return (
                    <tr>
                      <td style={{padding:"5px"}} width={"45%"} className="text-center">
                        {apareo.conexion.elemento.id}
                      </td>
                      <td style={{padding:"5px"}} width={"45%"} className="text-center">
                        {apareo.elementoRed.elemento.codigo}
                      </td>
                      <td style={{padding:"5px"}} width={"10%"} className="text-center">
                        <button type="button" onClick={(e) => { this.quitar(apareo) }}>X</button>
                      </td>
                    </tr>
                  )
                })}
              </tbody>
            </table>

            <div className="row" style={{margin:0,padding:0,paddingTop:"10px",borderTop:"solid 1px #ccc"}}>

              <div className="col-3">
                <div className="form-group">
                  <button type="button" onClick={(e) => { this.save() }}  disabled={this.state.saving ? true : false}>{this.state.saving ? "Guardando" : "Guardar"}</button>
                </div>
              </div>

              <div className="col-3">
                <div className="form-group">
                  <button type="button" onClick={(e) => {this.setState({apareos:[], apareoNuevoConexion:0, apareoNuevoElemento:0})}}>Cancelar</button>
                </div>
              </div>

            </div>

          </div>
          :
          <div className="row">
            <div className="col-12 text-center" style={{marginTop:'30px'}}> {"<< Seleccione conexión y elemento de red >>"} </div>
          </div>
      )
  }

  render(){
    return (
      <div className="row">

        <div className="col-12" >
          {this._renderSeleccionActual()}
        </div>

      </div>
    );
  }

  componentWillUnmount(){
    // quito los elementos del mapa de esta vista elementos de red en el mapa
    //this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='gestion-obra'))
  }
}
