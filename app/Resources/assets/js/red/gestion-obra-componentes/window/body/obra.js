import React, {Component} from 'react';
require('es6-promise').polyfill();
require('isomorphic-fetch');
const objectAssign = require('object-assign');
import Select from 'react-select';

import DayPicker from 'react-day-picker';
import DayPickerInput from 'react-day-picker/DayPickerInput';
import MomentLocaleUtils, {formatDate,parseDate} from 'react-day-picker/moment';
import 'moment/locale/es';

export class Obra extends Component {

  inside = require('point-in-polygon')
  linkElement = null

  constructor(props) {
    super(props)

    this.state = {
      "id": props.elementoId == undefined || props.elementoId == null ? 0 : props.elementoId,
      "nombre": '',
      "fechaInicioEstimada": '',
      "fechaFinEstimada": '',
      "georeferencias":[],
      "elementos":[],
      "elementosSinObra": [],
      "elementosNuevos":[],
      "elementosQuitar":[],
      "accionDrawingManagerPolygon":"",
      "saving": 0,
      "load": 0,
    }
  }

  componentWillMount() {

    // inicializo elementos de red en el mapa
    //this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='gestion-obra'))

    if (this.state.id != undefined && this.state.id != null && this.state.id != 0) {
      this.getObra(this.state.id)
    }

    this.getElementosRedSinObra()

  }

  componentWillReceiveProps = (nextProps) => {

    // para agregar o quitar elementos de red a la obra por click
    if(nextProps.markerClick!=null
      && this.props.markerClick!=nextProps.markerClick){

      this.gestionarElementosDeRedPorClick(nextProps.markerClick.marker)
    }


    // para agregar o quitar elementos de red a la obra por seleccion de area
    if(nextProps.drawingManagerPolygonComplete!=null && nextProps.consumidorHerramientaMapaActual==null
      && this.props.drawingManagerPolygonComplete!=nextProps.drawingManagerPolygonComplete){


      if (this.state.accionDrawingManagerPolygon=="agregar"){
        this.agregarElementosDeRedPorArea(nextProps.drawingManagerPolygonComplete)
      }

      if (this.state.accionDrawingManagerPolygon=="quitar"){
        this.quitarElementosDeRedPorArea(nextProps.drawingManagerPolygonComplete)
      }

    }

  }

  agregarElementosDeRedPorArea(drawing) {

    let polygon = drawing.getPath().getArray() != null ? drawing.getPath().getArray().map((e) => {return [e.lat(),e.lng()]}) : []
    let nuevoIndex = []

    let elementosNuevos = this.state.elementosNuevos
    let elementosSinObra = this.state.elementosSinObra

    this.state.elementosSinObra.map((el, index, array) => {

      if(this.inside([el.lastGeoreferencias.lat,el.lastGeoreferencias.lng], polygon)){
        elementosNuevos.push(el),
        elementosSinObra=elementosSinObra.filter(elFilter => elFilter.id !== el.id)
      }

    })

    this.setState({
      "elementosNuevos": elementosNuevos,
      "elementosSinObra":elementosSinObra
    }, () => {
      drawing.setMap(null)
      this.setMarkers()
      this.props.handleHerramientaMapaActual(["drawingManagerPolygon"]);
    })

  }

  quitarElementosDeRedPorArea(drawing) {

    let polygon = drawing.getPath().getArray() != null ? drawing.getPath().getArray().map((e) => {return [e.lat(),e.lng()]}) : []
    let nuevoIndex = []

    let elementosQuitar = this.state.elementosQuitar
    let elementos = this.state.elementos

    this.state.elementos.map((el, index, array) => {

      if (el.tipo=="actual" && el.elemento.estadoActual.estado == "EN_OBRA") {

        if(this.inside([el.lastGeoreferencias.lat,el.lastGeoreferencias.lng], polygon)){

          elementosQuitar.push(el),
          elementos=elementos.filter(elFilter => elFilter.id !== el.id)
        }
      }

    })

    this.setState({
      "elementosQuitar": elementosQuitar,
      "elementos":elementos
    }, () => {
      drawing.setMap(null)
      this.setMarkers()
      this.props.handleHerramientaMapaActual(["drawingManagerPolygon"]);
    })

  }

  gestionarElementosDeRedPorClick(el) {

    if (el.tipo=="sin-obra") {

      let elementosSinObra = this.state.elementosSinObra
      let elementosNuevos = this.state.elementosNuevos

      elementosNuevos.push(el.elemento)

      this.setState({
        "elementosSinObra": elementosSinObra.filter(elFilter => elFilter.id !== el.elemento.id),
        "elementosNuevos":elementosNuevos
      }, () => {
        this.setMarkers()
      })
    }

    if (el.tipo=="nuevo") {

      let elementosNuevos = this.state.elementosNuevos
      let elementosSinObra = this.state.elementosSinObra

      elementosSinObra.push(el.elemento)

      this.setState({
        "elementosNuevos": elementosNuevos.filter(elFilter => elFilter.id !== el.elemento.id),
        "elementosSinObra":elementosSinObra
      }, () => {
        this.setMarkers()
      })

    }


    if (el.tipo=="quitar") {

      let elementosQuitar = this.state.elementosQuitar
      let elementos = this.state.elementos

      elementos.push(el.elemento)

      this.setState({
        "elementosQuitar": elementosQuitar.filter(elFilter => elFilter.id !== el.elemento.id),
        "elementos":elementos
      }, () => {
        this.setMarkers()
      })

    }

    if (el.tipo=="actual" && el.elemento.estadoActual.estado == "EN_OBRA") {

      let elementos = this.state.elementos
      let elementosQuitar = this.state.elementosQuitar
      elementosQuitar.push(el.elemento)

      this.setState({
        "elementos": elementos.filter(elFilter => elFilter.id !== el.elemento.id),
        "elementosQuitar":elementosQuitar
      }, () => {
        this.setMarkers()
      })

    }

  }

  getObra = (id = 0) => {

    let url = this.props.uri + "obra/"+id
    fetch(url)
    .then(res => res.json())
    .then(data => {

      if(data.obra.id != undefined) {

        this.setState({
          "id":parseInt(data.obra.id),
          "nombre": data.obra.nombre != null ? data.obra.nombre : "",
          "fechaInicioEstimada": data.obra.fechaInicioEstimada != null ? data.obra.fechaInicioEstimada : "",
          "fechaFinEstimada": data.obra.fechaFinEstimada ? data.obra.fechaFinEstimada : "",
          "elementos": data.obra.elementos.length>0 ? data.obra.elementos : [],
          "load": this.state.load + 1,
          "elementosNuevos":[],
          "elementosQuitar":[]
        }, () => {
          this.setMarkers()
        })

      }

    })
  }

  setMarkers() {

    let p = this.state.elementosSinObra.filter((e) => {
      return e.elementoTipo.tipoGeoreferencia == "punto" && e.georeferencias && e.georeferencias.length > 0;
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
        "tipo":"sin-obra",
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
        "tipoMarker":"gestion-obra"
      }
    }).concat(this.state.elementos.filter((e) => {
      return e.elementoTipo.tipoGeoreferencia == "punto" && e.georeferencias && e.georeferencias.length > 0;
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
        "tipo":"actual",
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
        "tipoMarker":"gestion-obra"
      }
    }), this.state.elementosNuevos.map((e) => {
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
        "tipo":"nuevo",
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
        "tipoMarker":"gestion-obra"
      }
    }),
    this.state.elementosQuitar.map((e) => {
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
        "tipo":"quitar",
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
        "tipoMarker":"gestion-obra"
      }
    }))

    let promise = this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='gestion-obra' && e.tipoMarker!='elementos-red'))
    promise.then((success) => {
      let m = this.props.markers.concat(p)
      m = Array.from(new Set(m.map(JSON.stringify))).map(JSON.parse);
      this.props.setMarkers(m)
    })

  }

  getElementosRedSinObra() {

    let url = this.props.uri + "elementos-red-sin-obra"
    fetch(url, {
      method: 'GET'
    })
    .then(res => res.json())
    .then(data => {

      if (data.elementosSinObra.length > 0) {
        this.setState({
          "elementosSinObra":data.elementosSinObra
        }, () => {
          this.setMarkers()
        })
      }

    })
  }

  handleFormSubmit = (e) => {

    let valor = '';

    e.preventDefault();

    /*let valor = this.state.georeferencias.length
    if (valor == "" || valor == 0) {
      alert("Seleccione posición del elemento en el mapa")
      return
    }*/

    valor = this.state.nombre.replace(/ /g,"")
    if (valor == "") {
      alert("Ingrese nombre")
      return
    }


    valor = this.state.fechaInicioEstimada.replace(/ /g,"")
    if (valor == "") {
      alert("Ingrese fecha inicio estimada")
      return
    }

    valor = this.state.fechaFinEstimada.replace(/ /g,"")
    if (valor == "") {
      alert("Ingrese fecha fin estimada")
      return
    }

    let obra = {
      "id": this.state.id,
      "nombre": this.state.nombre,
      "fechaInicioEstimada": this.state.fechaInicioEstimada,
      "fechaFinEstimada": this.state.fechaFinEstimada,
      "elementosNuevos": this.state.elementosNuevos.map(e => e.id),
      "elementosQuitar": this.state.elementosQuitar.map(e => e.id),
    }

    let url = this.props.uri + "obra/save"
    fetch(url, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        "obra": obra,
    })})
    .then(res => res.json())
    .then(data => {

      if (data.resultado == "ok" && data.obra.id != null) {
        this.setState({
          "id": data.obra.id
        })
        alert("Cambios guardados con éxito")

        this.getObra(data.obra.id)
        this.getElementosRedSinObra()

      } else {
        alert("ERROR! No se guardaron los cambios")
      }

    })

  }

  handleChange = (e) => {

    let eTarget = e.target
    this.setState({[eTarget.name]: eTarget.value})
  }

  handleOpciones(opcion,values,e) {

    if (opcion == "cancelar") {
      this.getObra(this.state.id)
      this.getElementosRedSinObra()
    }

    e.preventDefault();
  }

  render() {

    const background = ["#9e9e9e","#bdbdbd","#e0e0e0","#eeeeee","#f5f5f5","#fafafa","#fff","#fff","#fff","#fff","#fff","#fff"]
    const color = ["#fff","#fff","#666","#666","#666","#666","#333","#333","#333","#333","#333","#333"]

    return (

      <div className="elemento">

        <form onSubmit={this.handleFormSubmit}>

          <div className="form-group">
            <label>Nombre</label>
            <input value={this.state.nombre} onChange={(e) => this.handleChange(e)} name="nombre" type="text" className="form-control" placeholder="" />
            <small></small>
          </div>

          <div className="form-group">
            <label>Fecha inicio estimada</label>
            <DayPickerInput
              value={this.state.fechaInicioEstimada}
              formatDate={formatDate}
              parseDate={parseDate}
              dayPickerProps={{
                locale: 'es',
                localeUtils: MomentLocaleUtils,
              }}

              onDayChange={(fecha)=>{ this.setState({"fechaInicioEstimada": `${formatDate(fecha, 'YYYY-MM-DD', 'YYYY-MM-DD')}`}) }}
            />
            <small></small>
          </div>

          <div className="form-group">
            <label>Fecha fin estimada</label>
            <DayPickerInput
              value={this.state.fechaFinEstimada}
              formatDate={formatDate}
              parseDate={parseDate}
              dayPickerProps={{
                locale: 'es',
                localeUtils: MomentLocaleUtils,
              }}

              onDayChange={(fecha)=>{ this.setState({"fechaFinEstimada": `${formatDate(fecha, 'YYYY-MM-DD', 'YYYY-MM-DD')}`}) }}
            />
            <small></small>
          </div>

        <div className="form-group">

          <label>Elementos de red</label>

          <nav>
            <div className="nav nav-tabs" id="nav-tab" role="tablist">
              <a className="nav-item nav-link active" ref={(element) => {this.linkElement=element}} style={{padding:"3px 10px"}} id="nav-actuales-tab" onClick={() => { this.setState({"accionDrawingManagerPolygon":""}); this.props.handleHerramientaMapaActual([]); }} data-toggle="tab" href="#nav-actuales" role="tab" aria-controls="nav-actuales" aria-selected="true">
                ({this.state.elementos.length}) Actuales
              </a>
              <a className="nav-item nav-link" style={{padding:"3px 10px"}} id="nav-agregar-tab" onClick={() => { this.setState({"accionDrawingManagerPolygon":"agregar"}); this.props.handleHerramientaMapaActual(["drawingManagerPolygon"]); }} data-toggle="tab" href="#nav-agregar" role="tab" aria-controls="nav-profile" aria-selected="false">
                ({this.state.elementosNuevos.length}) Agregar
              </a>
              <a className="nav-item nav-link" style={{padding:"3px 10px"}} id="nav-quitar-tab" onClick={() => { this.setState({"accionDrawingManagerPolygon":"quitar"}); this.props.handleHerramientaMapaActual(["drawingManagerPolygon"]); }} data-toggle="tab" href="#nav-quitar" role="tab" aria-controls="nav-profile" aria-selected="false">
                ({this.state.elementosQuitar.length}) Quitar
              </a>
            </div>
          </nav>

          <div className="tab-content" id="nav-tabContent">
            <div className="tab-pane fade show active" id="nav-actuales" role="tabpanel" aria-labelledby="nav-actuales-tab">

              <div style={{maxHeight:"250px", overflowX:"hidden", overflowY:"auto", padding:"5px"}}>
                {this.state.elementos.length==0 && "Sin elementos de red"}
                {this.state.elementos.length>0 && this.state.elementos.map((e) => {
                  return (
                    <div  className="row" style={{margin:"2px 0px 0px "+(e.lvl*4)+"px",color:color[e.lvl],background:background[e.lvl],borderLeft:"solid 5px #"+e.elementoTipo.colorHexa}}  key={e.id}>

                      <div className="col-10" >
                        <div className="row">

                          <div className="col-12">

                            <a href="javascript:void(0)" style={{color:color[e.lvl]}} className="title" >
                              {e.codigo}
                            </a>

                          </div>

                          <div className="col-12" >
                            <span className="referencia">
                              {"L"+(e.lvl+1)}
                            </span> &nbsp;/&nbsp;
                            <span className="referencia badge">
                              {e.elementoTipo.nombre}
                            </span>
                          </div>

                        </div>
                      </div>

                      <div className="col-1" >
                        <a href="javascript:void(0)" onClick={() => {this.gestionarElementosDeRedPorClick({tipo:"actual",elemento:e})}} className="btn" style={{color:color[e.lvl]}}>
                          <i className="fa fa-trash"></i>
                        </a>
                      </div>

                    </div>
                  )
                })}
              </div>
            </div>

            <div className="tab-pane fade" id="nav-agregar" role="tabpanel" aria-labelledby="nav-agregar-tab">

              <div style={{maxHeight:"250px", overflowX:"hidden", overflowY:"auto", padding:"5px"}}>
                {this.state.elementosNuevos.length==0 && "No hay elementos de red para agregar"}
                {this.state.elementosNuevos.length>0 && this.state.elementosNuevos.map((e) => {
                  return (
                    <div  className="row" style={{margin:"2px 0px 0px "+(e.lvl*4)+"px",color:color[e.lvl],background:background[e.lvl],borderLeft:"solid 5px #"+e.elementoTipo.colorHexa}}  key={e.id}>

                      <div className="col-10" >
                        <div className="row">

                          <div className="col-12">

                            <a href="javascript:void(0)" style={{color:color[e.lvl]}} className="title" >
                              {e.codigo}
                            </a>

                          </div>

                          <div className="col-12" >
                            <span className="referencia">
                              {"L"+(e.lvl+1)}
                            </span> &nbsp;/&nbsp;
                            <span className="referencia badge">
                              {e.elementoTipo.nombre}
                            </span>
                          </div>

                        </div>
                      </div>

                      <div className="col-1" >
                        <a href="javascript:void(0)" onClick={() => {this.gestionarElementosDeRedPorClick({tipo:"nuevo",elemento:e})}} className="btn" style={{color:color[e.lvl]}}>
                          <i className="fa fa-trash"></i>
                        </a>
                      </div>

                    </div>
                  )
                })}
              </div>

            </div>

            <div className="tab-pane fade" id="nav-quitar" role="tabpanel" aria-labelledby="nav-quitar-tab">

              <div style={{maxHeight:"250px", overflowX:"hidden", overflowY:"auto", padding:"5px"}}>
                {this.state.elementosQuitar.length==0 && "No hay elementos de red para quitar"}
                {this.state.elementosQuitar.length>0 && this.state.elementosQuitar.map((e) => {

                  return (
                    <div  className="row" style={{margin:"2px 0px 0px "+(e.lvl*4)+"px",color:color[e.lvl],background:background[e.lvl],borderLeft:"solid 5px #"+e.elementoTipo.colorHexa}}  key={e.id}>

                      <div className="col-10" >
                        <div className="row">

                          <div className="col-12">

                            <a href="javascript:void(0)" style={{color:color[e.lvl]}} className="title" >
                              {e.codigo}
                            </a>

                          </div>

                          <div className="col-12" >
                            <span className="referencia">
                              {"L"+(e.lvl+1)}
                            </span> &nbsp;/&nbsp;
                            <span className="referencia badge">
                              {e.elementoTipo.nombre}
                            </span>
                          </div>

                        </div>
                      </div>

                      <div className="col-1" >
                        <a href="javascript:void(0)" onClick={() => {this.gestionarElementosDeRedPorClick({"tipo":"quitar","elemento":e})}} className="btn" style={{color:color[e.lvl]}}>
                          <i className="fa fa-trash"></i>
                        </a>
                      </div>

                    </div>
                  )
                })}
              </div>

            </div>

          </div>


          </div>

          <div className="row" style={{margin:0,padding:0,paddingTop:"10px",borderTop:"solid 1px #ccc"}}>
            <div className="col-3">
              <div className="form-group">
                <button type="submit" disabled={this.state.saving ? true : false}>{this.state.saving ? "Guardando cambios..." : "Guardar"}</button>
              </div>
            </div>

            <div className="col-3">
              <div className="form-group">
                <button onClick={(e) => this.handleOpciones("cancelar", [], e)}>Cancelar</button>
              </div>
            </div>

            <div className="col-3" >
              <div className="dropdown dropup">
                <button type="button" className="dropdown-toggle" data-toggle="dropdown">
                  <i className="fa fa-ellipsis-h" aria-hidden="true"></i>
                </button>
                <div className="dropdown-menu dropdown-menu-right">
                  <a className="dropdown-item"
                   href="javascript:void(0)"
                   onClick={()=> {this.eliminar()}}
                  >
                      Eliminar
                  </a>
                </div>
              </div>

            </div>

          </div>

        </form>

      </div>
    )
  }

  componentWillUnmount() {
    // quito los elementos del mapa de esta vista elementos de red en el mapa
    this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='gestion-obra'))
  }
}
