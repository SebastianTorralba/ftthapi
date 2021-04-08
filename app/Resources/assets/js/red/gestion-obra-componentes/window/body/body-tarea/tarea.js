import React, {Component} from 'react';
require('es6-promise').polyfill();
require('isomorphic-fetch');
const objectAssign = require('object-assign');
import Select from 'react-select';

import DayPicker from 'react-day-picker';
import DayPickerInput from 'react-day-picker/DayPickerInput';
import MomentLocaleUtils, {formatDate,parseDate} from 'react-day-picker/moment';
import 'moment/locale/es';

export class Tarea extends Component {

  inside = require('point-in-polygon')

  constructor(props) {
    super(props)

    let hoy = new Date()

    this.state = {
      "id": props.elementoId == undefined || props.elementoId == null ? 0 : props.elementoId,
      "cuadrilla": [],
      "observacion": "",
      "tipo":[],
      "fecha": hoy.getFullYear()+"-" +("0" + (hoy.getMonth()+1)).slice(-2)+"-" + ("0" + hoy.getDate()).slice(-2),
      "obra": this.props.obraId,
      "elementos":[],
      "elementosObra": [],
      "elementosNuevos":[],
      "elementosQuitar":[],
      "saving": 0,
      "load": 0,
    }

    this.getCuadrilla()
  }

  componentWillMount() {

    this.getTarea(this.state.id)
    this.props.handleHerramientaMapaActual("");

  }

  componentWillReceiveProps = (nextProps) => {

    if(nextProps.markerClick!=null
      && this.props.markerClick!=nextProps.markerClick){

      this.gestionarElementosDeRedPorClick(nextProps.markerClick.marker)
    }

    if(nextProps.drawingManagerPolygonComplete!=null
      && this.props.drawingManagerPolygonComplete!=nextProps.drawingManagerPolygonComplete){

      this.props.handleHerramientaMapaActual("");

      if (this.state.accionDrawingManagerPolygon=="agregar"){
        this.agregarElementosDeRedPorArea(nextProps.drawingManagerPolygonComplete)
      }

      if (this.state.accionDrawingManagerPolygon=="quitar"){
        this.quitarElementosDeRedPorArea(nextProps.drawingManagerPolygonComplete)
      }

    }
  }

  componentWillUpdate() {}

  agregarElementosDeRedPorArea(drawing) {

    let polygon = drawing.getPath().getArray() != null ? drawing.getPath().getArray().map((e) => {return [e.lat(),e.lng()]}) : []
    let nuevoIndex = []

    let elementosNuevos = this.state.elementosNuevos
    let elementosObra = this.state.elementosObra

    this.state.elementosObra.map((el, index, array) => {

      if (
        ((el.estadoActual.estado == "EN_OBRA" || el.estadoActual.estado == "INSTALADO") && this.state.tipo.value == "INSTALACION_FUSION") ||
        (el.estadoActual.estado == "EN_OBRA" && this.state.tipo.value == "INSTALACION") ||
        (el.estadoActual.estado == "INSTALADO" && this.state.tipo.value == "FUSION")
      ) {

        if(this.inside([el.lastGeoreferencias.lat,el.lastGeoreferencias.lng], polygon)){
          elementosNuevos.push(el),
          elementosObra=elementosObra.filter(elFilter => elFilter.id !== el.id)
        }

      }

    })

    this.setState({
      "elementosNuevos": elementosNuevos,
      "elementosObra":elementosObra
    }, () => {
      drawing.setMap(null)
      this.setMarkers()
      this.props.handleHerramientaMapaActual("drawingManagerPolygon");
    })

  }

  quitarElementosDeRedPorArea(drawing) {

    let polygon = drawing.getPath().getArray() != null ? drawing.getPath().getArray().map((e) => {return [e.lat(),e.lng()]}) : []
    let nuevoIndex = []

    let elementosQuitar = this.state.elementosQuitar
    let elementos = this.state.elementos

    this.state.elementos.map((el, index, array) => {

      if (el.estadoActual.estado != "EN_OBRA_CON_TAREA") return

      if(this.inside([el.lastGeoreferencias.lat,el.lastGeoreferencias.lng], polygon)){

        elementosQuitar.push(el),
        elementos=elementos.filter(elFilter => elFilter.id !== el.id)
      }

    })

    this.setState({
      "elementosQuitar": elementosQuitar,
      "elementos":elementos
    }, () => {
      drawing.setMap(null)
      this.setMarkers()
      this.props.handleHerramientaMapaActual("drawingManagerPolygon");
    })

  }

  gestionarElementosDeRedPorClick(el) {

    if (el.tipo=="sin-obra" &&
      (((el.elemento.estadoActual.estado == "EN_OBRA" || el.elemento.estadoActual.estado == "INSTALADO") && this.state.tipo.value == "INSTALACION_FUSION") ||
      (el.elemento.estadoActual.estado == "EN_OBRA" && this.state.tipo.value == "INSTALACION") ||
      (el.elemento.estadoActual.estado == "INSTALADO" && this.state.tipo.value == "FUSION"))
    ) {

      let elementosObra = this.state.elementosObra
      let elementosNuevos = this.state.elementosNuevos

      elementosNuevos.push(el.elemento)

      this.setState({
        "elementosObra": elementosObra.filter(elFilter => elFilter.id !== el.elemento.id),
        "elementosNuevos":elementosNuevos
      }, () => {
        this.setMarkers()
      })
    }

    if (el.tipo=="nuevo") {

      let elementosNuevos = this.state.elementosNuevos
      let elementosObra = this.state.elementosObra

      elementosObra.push(el.elemento)

      this.setState({
        "elementosNuevos": elementosNuevos.filter(elFilter => elFilter.id !== el.elemento.id),
        "elementosObra":elementosObra
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

    if (el.tipo=="actual" && el.elemento.estadoActual.estado == "EN_OBRA_CON_TAREA") {

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

  getTarea = (id = 0) => {

    if (id > 0) {

      let url = this.props.uri + "obra/"+this.props.obraId+"/tarea/"+id
      fetch(url)
      .then(res => res.json())
      .then(data => {

        if(data.tarea.id != undefined) {

          this.setState({
            "id":parseInt(data.tarea.id),
            "cuadrilla": data.tarea.cuadrilla,
            "observacion": data.tarea.observacion,
            "tipo": data.tarea.tipo,
            "fecha": data.tarea.fecha != "" ? data.tarea.fecha : new Date(),
            //"elementos": data.tarea.elementos.length > 0 ? data.tarea.elementos.map(el => Object.assign({}, el.elemento, {"tareaElementoId":el.tareaElementoId, "estadoTarea":el.estadoTarea})) : [],
            "elementos": data.tarea.elementos.length > 0 ? data.tarea.elementos : [],
            "elementosNuevos": [],
            "elementosQuitar": [],
            "saving": 0,
            "load": 0,
          }, () => {
            this.setMarkers()
          })

        }

      })

    }

    this.getElementosRedObra()
  }

  setTareaState(tarea) {

  }

  getElementosRedObra() {

    let url = this.props.uri + "obra/"+this.props.obraId+"/elementos-red-obra"
    fetch(url, {
      method: 'GET'
    })
    .then(res => res.json())
    .then(data => {

      this.setState({
        "elementosObra":data.elementosObra
      }, () => {
        this.setMarkers()
      })

    })
  }

  getCuadrilla() {

    let url = this.props.uri + "cuadrillas-activas"
    fetch(url, {
      method: 'GET'
    })
    .then(res => res.json())
    .then(data => {

      if (data.cuadrillas.length > 0) {
        this.setState({
          "cuadrillas":data.cuadrillas
        })
      }

    })
  }

  handleFormSubmit = (e) => {

    e.preventDefault();

    let valor = this.state.tipo.length
    if (valor == "" || valor == 0) {
      alert("Seleccione tipo tarea")
      return
    }

    valor = this.state.cuadrilla.length
    if (valor == "" || valor == 0) {
      alert("Seleccione cuadrilla")
      return
    }

    valor = this.state.fecha.replace(/ /g,"")
    if (valor == "") {
      alert("Ingrese fecha")
      return
    }

    let tarea = {
      "id": this.state.id,
      "obra": this.state.obra,
      "cuadrilla": this.state.cuadrilla ? this.state.cuadrilla.id : 0,
      "tipo": this.state.tipo,
      "observacion": this.state.observacion,
      "fecha": this.state.fecha,
      "elementosNuevos": this.state.elementosNuevos.map(e => e.id),
      "elementosQuitar": this.state.elementosQuitar.map(e => e.obra.tareas[0].id),
    }

    let url = this.props.uri + "obra/"+tarea.obra+"/tarea/"+tarea.id+"/save"
    fetch(url, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        "tarea": tarea,
    })})
    .then(res => res.json())
    .then(data => {

      if (data.resultado == "ok" && data.tarea.id != null) {

        this.setState({
          "id": data.tarea.id
        })

        alert("Cambios guardados con éxito")

        this.getTarea(data.tarea.id)

      } else {
        alert("ERROR! No se guardaron los cambios")
      }

    })

  }

  eliminar = () => {

    if (this.state.elementoIdValue != '' && this.state.elementos.length == 0) {
      if (confirm('¿Seguro desea eliminar tarea?')) {

        let url = this.props.uri + "obra/"+this.props.obraId+"/tarea/"+this.state.id+"/eliminar"

        fetch(url, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            "id":this.obra,
            "tareaId":this.state.id,
        })})
        .then(res => res.json())
        .then(data => {

          if(data.resultado == "ok") {

            alert("Tarea # "+ this.state.id + " fue eliminada")
            this.props.handleOpcionMenuTareaActual({"id": "tarea-listado","params":{"id":0}})

          } else {
            alert("No se pudo eliminar tarea")
          }

        })


      }
    } else {
      alert("No se pudo eliminar tarea debido a que la misma tiene elementos asignados")
    }
  }

  finalizar = (te) => {
console.log(te)
    if (confirm('¿Seguro desea finalizar la tarea para el elemento '+te.codigo+'?')) {

      let url = this.props.uri + "obra/"+this.props.obraId+"/tarea/"+this.state.id+"/elemento/"+te.obra.tareas[0].id+"/finalizar"

      fetch(url, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          "id":this.props.obraId,
          "tareaId":this.state.id,
          "tareaElementoId":te.obra.tareas[0].id
      })})
      .then(res => res.json())
      .then(data => {

        if(data.resultado == "ok") {

          let nes = this.state.elementos.map((e) => {

            if (JSON.stringify(e) == JSON.stringify(te) ) {
              e.estadoActual.estado = data.elementoEstado;
              e.obra.tareas[0].estado = "FINALIZADA";
            }

            return e
          })

          this.setState({elementos: nes}, () => {
            this.setMarkers()
          })

          alert("La operación para " + te.codigo + " fue realizada con éxito")

        } else {
          alert("No se pudo realizar la operación solicitada")
        }

      })

    }

  }

  setMarkers() {

    let p = this.state.elementosObra.map((e) => {
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
    }).concat(this.state.elementos.map((e) => {

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

  handleSelectTipo = (data) => {

    this.setState({"tipo": data}, () => {
      this.setMarkers()
    })
  }

  render() {

    const background = ["#9e9e9e","#bdbdbd","#e0e0e0","#eeeeee","#f5f5f5","#fafafa","#fff","#fff","#fff","#fff","#fff","#fff"]
    const color = ["#fff","#fff","#666","#666","#666","#666","#333","#333","#333","#333","#333","#333"]

    return (
      <div style={{position:'absolute',height: '85%', width: '100%', overflowY: 'auto'}}  >

        <form onSubmit={this.handleFormSubmit}>

          <div className="form-group">

            <label>Tipo</label>

            <Select
              isDisabled={this.state.id != 0 ? true : false}
              placeholder={'Seleccione tipo de tarea'}
              isMulti={false}
              value={this.state.tipo}
              options={[{"label":"Instalación","value":"INSTALACION"},{"label":"Fusión","value":"FUSION"},{"label":"Instalación y fusión","value":"INSTALACION_FUSION"}]}
              onChange={(data)=>{ this.handleSelectTipo(data)} }
            />

            <small></small>
          </div>

          <div className="form-group">

            <label>Cuadrilla</label>
            <Select
              placeholder={'Seleccione cuadrilla'}
              isMulti={false}
              value={this.state.cuadrilla}
              options={this.state.cuadrillas}
              onChange={(data)=>{ this.setState({"cuadrilla": data})}}
            />
            <small></small>
          </div>

          <div className="form-group">
            <label>Fecha</label>
            <DayPickerInput

              value={this.state.fecha}
              formatDate={formatDate}
              parseDate={parseDate}
              dayPickerProps={{
                locale: 'es',
                localeUtils: MomentLocaleUtils,
              }}

              onDayChange={(fecha)=>{ this.setState({"fecha": `${formatDate(fecha, 'YYYY-MM-DD', 'YYYY-MM-DD')}`}) }}
            />
            <small></small>
          </div>

          <div className="form-group">
            <label>Observación</label>
            <textarea value={this.state.observacion} onChange={(e)=>{ this.setState({"observacion": e.target.value}) }} name="observacion" className="form-control" placeholder="" >
            </textarea>
            <small></small>
          </div>

          <div className="form-group">

            <label>Elementos de red</label>

            <nav>
              <div className="nav nav-tabs" id="nav-tab" role="tablist">
                <a className="nav-item nav-link active" style={{padding:"3px 10px"}} id="nav-actuales-tab" onClick={() => { this.setState({"accionDrawingManagerPolygon":""}); this.props.handleHerramientaMapaActual(""); }} data-toggle="tab" href="#nav-actuales" role="tab" aria-controls="nav-actuales" aria-selected="true">
                  ({this.state.elementos.length}) Actuales
                </a>
                <a className="nav-item nav-link" style={{padding:"3px 10px"}} id="nav-agregar-tab" onClick={() => { this.setState({"accionDrawingManagerPolygon":"agregar"}); this.props.handleHerramientaMapaActual("drawingManagerPolygon"); }} data-toggle="tab" href="#nav-agregar" role="tab" aria-controls="nav-profile" aria-selected="false">
                  ({this.state.elementosNuevos.length}) Agregar
                </a>
                <a className="nav-item nav-link" style={{padding:"3px 10px"}} id="nav-quitar-tab" onClick={() => { this.setState({"accionDrawingManagerPolygon":"quitar"}); this.props.handleHerramientaMapaActual("drawingManagerPolygon"); }} data-toggle="tab" href="#nav-quitar" role="tab" aria-controls="nav-profile" aria-selected="false">
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

                        <div className="col-7" >
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
                        { e.obra.tareas[0].estado == 'FINALIZADA' ?
                          <div className="col-3 pull-right" style={{color:'#fff', padding:'15px', fontSize:"14px;"}} title="Tarea finalizada para este elemento">
                            <i class="fa fa-check pull-right"  aria-hidden="true"></i>
                          </div>
                          :
                          <div className="col-5 pull-right" >
                            <a href="javascript:void(0)" title="Quitar elemento de esta tarea" onClick={() => {this.gestionarElementosDeRedPorClick({tipo:"actual",elemento:e})}} className="btn pull-right" style={{color:color[e.lvl]}}>
                              <i className="fa fa-trash"></i>
                            </a>
                            <a href="javascript:void(0)" title="Finalizar tarea para este elemento" onClick={() => { this.finalizar(e) }} className="btn pull-right" style={{color:color[e.lvl]}}>
                              F
                            </a>
                          </div>
                        }

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
                <button type="button" onClick={(e) => {this.getTarea(this.state.id); }}>Cancelar</button>
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
