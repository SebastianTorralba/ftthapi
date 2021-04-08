import React, {Component} from 'react';
require('es6-promise').polyfill();
require('isomorphic-fetch');
const objectAssign = require('object-assign');
import Select from 'react-select';

export class Elemento extends Component {

  elementosTipos = []
  elementos = []

  constructor(props) {
    super(props)

    this.state = {
      "id": props.elementoId == undefined || props.elementoId == null ? 0 : props.elementoId,
      "tipo": {},
      "nombre": '',
      "padre": {id:0},
      "punto":{lat:"", lng:""},
      "traza": {"georeferencias": []},
      "atributos": [],
      "georeferencias": [],
      "saving": 0,
      "load": 0
    }
  }

  componentWillMount() {

    this.getElementos();
    this.getElementosTipos()

    if (this.state.id > 0) {
      this.getElemento(this.state.id)
    }

  }

  componentWillReceiveProps = (nextProps) => {

    let tipoGeoreferencia = this.getTipoGeoreferenciaElementoSeleccionado();

     if (nextProps.mapClick!=this.props.mapClick && nextProps.mapClick !== null && nextProps.mapClick.latLng !== null ) {
       this.setPosition({lat: nextProps.mapClick.latLng.lat(), lng: nextProps.mapClick.latLng.lng()})
     }
  }

  setPosition = (latLng) => {


    let tipoGeoreferencia = this.getTipoGeoreferenciaElementoSeleccionado();

    // para eliminar la útima georeferencia
    if (latLng == -1 && this.state.georeferencias !=null && this.state.georeferencias.length > 0) {

      if (tipoGeoreferencia == 'punto') {

          this.setState({
            "punto": {lat:"", lng:""},
            "traza": {"georeferencias": []},
            "georeferencias": []
          }, () => {
            this.setMarkers()
          })
      }


      if (tipoGeoreferencia == 'traza') {

        let trazaLess = this.state.traza
        trazaLess.georeferencias = this.state.georeferencias.slice(0,-1)

        this.setState({
          "punto": {lat:"", lng:""},
          "traza": trazaLess,
          "georeferencias": this.state.georeferencias.slice(0,-1)
        }, () => {
            this.setPolylines()
        })
      }

      return
    }

    // para agregar georeferencia
    if (latLng != null && latLng != -1) {

      let tipoElemento = this.elementosTipos.filter((elementoTipo) => {
        return elementoTipo.id == this.state.tipo.id
      })

      if (tipoGeoreferencia == 'punto') {

          this.setState({
            "punto": objectAssign(latLng, tipoElemento[0]),
            "traza": {"georeferencias": []},
            "georeferencias": [latLng]
          }, () => {
            this.setMarkers()
          })
      }

      if(tipoGeoreferencia == 'traza') {

        if (latLng.lat != "" && latLng.lng != "") {

          this.setState({
            "punto": {lat:"", lng:""},
            "traza": objectAssign({"georeferencias": this.state.georeferencias.concat(latLng)}, tipoElemento[0]),
            "georeferencias": this.state.georeferencias.concat(latLng)
          }, () => {
            this.setPolylines()
          })

        }

      }
    }
  }

  eliminar = () => {

    if (this.state.elementoIdValue != '') {
      if (confirm('¿Seguro desea eliminar "'+ this.state.nombre + '"?')) {

        let url = this.props.uri + "eliminar/elemento"

        fetch(url, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            "id":this.state.id,
        })})
        .then(res => res.json())
        .then(data => {

          if(data.resultado == "ok") {

            this.setState({
              "punto": {lat:"", lng:""},
              "traza": {"georeferencias": []},
              "georeferencias": []
            })

            alert(this.state.nombre + " fue eliminado")

            this.props.handleOpcionMenuActual({
              "id": "grupo-listar",
            })

          } else {

            alert("No se pudo eliminar " + this.state.nombre)
          }

        })


      }
    }
  }

  getElementos = () => {

    let url = this.props.uri + "elementos"
    this.elementos = [];

    fetch(url)
    .then(res => res.json())
    .then(data => {

      this.elementos = data.elementos

      this.setState({
        "padre": {id:0},
        "load": this.state.load + 1
      })

      this.setMarkers()
      this.setPolylines()

    })

  }

  getElementosTipos = () => {

    let url = this.props.uri + "get-tipos-elementos"
    this.elementosTipos = [{}];

    fetch(url)
    .then(res => res.json())
    .then(data => {

        this.elementosTipos = data.tiposElementos
        this.setState({
          "tipo": data.tiposElementos.length > 0 ? data.tiposElementos[0] : {},
        })

        this.setState({
          "load": this.state.load + 1
        })

        // luego de que cargo el tipo de elemento, cargo los atributos para ese tipo de elemento
        // si es una edición, cargo el elemento seleccionado
        if (this.props.elementoId == 0) {
          this.getAtributos()
        }

    })
  }

  getAtributos = () => {

    let url = this.props.uri + "get-tipo-elemento-atributo/"+this.state.tipo.id

    fetch(url)
    .then(res => res.json())
    .then(data => {

        this.setState({
          "atributos": data.atributos.length > 0 ? data.atributos : []
        }, ()=>{

          this.setState({
            "load": this.state.load + 1
          })

        })

    })
  }

  getElemento = (id = 0) => {

    let url = this.props.uri + "elemento/"+id
    fetch(url)
    .then(res => res.json())
    .then(data => {

      if(data.elemento.id != undefined) {

        this.setState({
          "id":parseInt(data.elemento.id),
          "tipo": data.elemento.tipoElemento,
          "nombre": data.elemento.nombre != null ? data.elemento.nombre : "",
          "padre": data.elemento.parent,
          "georeferencias": data.elemento.georeferencias,
          "atributos": data.elemento.atributos
        }, () => {

          this.getAtributos()

          if (data.elemento.tipoElemento.tipoGeoreferencia == "punto") {

              this.setState({
                "punto": data.elemento.lastGeoreferencias
              }, () => {
                this.setMarkers()
              })

              this.props.onMapZoom(18)
              this.props.handleMapDefaultCenter(data.elemento.lastGeoreferencias)

          } else {


            this.setState({
              "traza": {"georeferencias": data.elemento.georeferencias}
            }, () => {
              this.setPolylines()
            })

            this.props.onMapZoom(18)
            this.props.handleMapDefaultCenter(data.elemento.lastGeoreferencias)

          }

        })

      }

    })
  }

  getTipoGeoreferenciaElementoSeleccionado() {

   let elementoTipoSeleccionado = this.elementosTipos.filter((elementoTipo) => {
     return elementoTipo.id == this.state.tipo.id
   })

   if (elementoTipoSeleccionado.length > 0) {
     return elementoTipoSeleccionado[0].tipoGeoreferencia
    }

    return null
 }

  setMarkers() {

    let p = this.elementos.filter((e) => {
      return this.state.id!=e.id && e.elementoTipo.tipoGeoreferencia == "punto" && e.georeferencias && e.georeferencias.length > 0;
    }).map((e) => {

      return {
        "id":e.id,
        "visible": 1,
        "lat":parseFloat(e.georeferencias[0].latitud),
        "lng":parseFloat(e.georeferencias[0].longitud),
        "image": '/uploads/imagenes/tipo-elemento/'+e.elementoTipo.id+"_null_"+this.props.dimensionMarker+".png",
        "opacity":1,
        "obra_id":0,
        "title":e.codigo+" - "+e.nombre+" - "+e.estadoActual.estado,
        "minZoom": 0,
        "tipo":"punto",
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

    if (this.state.punto.lat!="") {

      p = p.concat([{
        "id":this.state.id,
        "visible": 1,
        "lat":this.state.punto.lat,
        "lng":this.state.punto.lng,
        "image": '/uploads/imagenes/tipo-elemento/'+this.state.tipo.id+"_base_"+this.props.dimensionMarker+".png",
        "opacity":1,
        "title":this.state.nombre,
        "minZoom": 0,
        "tipo":"punto",
        "elemento": {
            "resumen":
              {"categoria":"Red",
               "elementos": [{
                  "id":0,
                  "descripcion":this.state.nombre,
                  "estado":"Creando",
                  "cantidad":1,
                  "unidad":""
                }]
              }

        },
        "tipoMarker":"elemento-red"
      }])

    }

    let promise = this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='elementos-red' && e.tipoMarker!='elemento-red'))
    promise.then((success) => {
      let m = this.props.markers.concat(p)
      m = Array.from(new Set(m.map(JSON.stringify))).map(JSON.parse);
      this.props.setMarkers(m)
    })

  }

  setPolylines() {

    let p = this.elementos.filter((e) => {
      return this.state.id!=e.id && e.elementoTipo.tipoGeoreferencia == "traza" && e.georeferencias && e.georeferencias.length > 0;
    }).map((e) => {

      return {
        "id":e.id,
        "visible": 1,
        "path":e.georeferencias,
        "color": "#444",
        "title":e.codigo+" - "+e.nombre+" - "+e.estadoActual.estadoNombre,
        "tipo":"traza",
        "tipoPolyline":"elementos-red"
      }

    });

    if (this.state.traza.georeferencias.length>0) {

      p = p.concat([{
        "id":this.state.id,
        "visible": 1,
        "path":this.state.traza.georeferencias,
        "color": "#"+this.state.tipo.color,
        "title":this.state.nombre,
        "tipo":"traza",
        "tipoPolyline":"elemento-red"
      }])

    }

    let promise = this.props.setPolylines(this.props.polylines.filter((e) => e.tipoPolyline!='elementos-red' && e.tipoPolyline!='elemento-red'))
    promise.then((success) => {
      let m = this.props.polylines.concat(p)
      m = Array.from(new Set(m.map(JSON.stringify))).map(JSON.parse);
      this.props.setPolylines(m)
    })

  }

  handleFormSubmit = (e) => {

    e.preventDefault();

    let valor = this.state.georeferencias.length
    if (valor == "" || valor == 0) {
      alert("Seleccione posición del elemento en el mapa")
      return
    }

    valor = this.state.tipo.id
    if (valor == "" || valor == 0) {
      alert("Seleccione tipo de elemento")
      return
    }

    valor = this.state.nombre.replace(/ /g,"")
    if (valor == "") {
      alert("Ingrese nombre")
      return
    }

    let elemento = {
      "id": this.state.id,
      "elementoPadre": this.state.padre != null && this.state.padre.id != undefined ? this.state.padre.id : 0 ,
      "elementoTipo": this.state.tipo.id,
      "nombre": this.state.nombre,
      "atributos": this.state.atributos.map((atributo) => {

        let value = atributo.value == null ? "" : atributo.value;

        return [atributo.id, value]
      }),
      "georeferencias": this.state.georeferencias.map((geo) => {
        return [geo.lat,geo.lng]
      })
    }

    let url = this.props.uri + "elemento/save"
    fetch(url, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        "elemento": elemento,
    })})
    .then(res => res.json())
    .then(data => {

      if (data.resultado == "ok" && data.elemento.id != null) {
        this.setState({
          "id": data.elemento.id
        })
        alert("Cambios guardados con éxito")
      } else {
        alert("ERROR! No se guardaron los cambios")
      }

    })

  }

  handleChangeSelectTipo = (data) => {

    this.setState({
      "tipo": data,

    }, () => {

      // seteo el pare que estaba elegido para que lo vuelva a seleccionar
      this.setState({"padre":{id:0}})

      if (this.props.mapClick) {
        this.setPosition({lat:"", lng:""})
      }

      // cargo los atributos asociados al tipo de elemento
      this.getAtributos();

    })

  }

  handleChangeSelectPadre = (data) => {

    this.setState({
      "padre": data,

    }, () => {

      //this.setPosition({lat: (parseFloat(data.lastGeoreferencias.lat)+0.000230000000), lng: (parseFloat(data.lastGeoreferencias.lng)+0.000230000000)})
    })

  }

  handleChange = (e) => {

    let eTarget = e.target
    this.setState({[eTarget.name]: eTarget.value})
  }

  handleChangeAtributo = (e) => {

    let idAttCambio = e.target.getAttribute("data-id");
    let value = e.target.value;

    this.setState({
      "atributos": this.state.atributos.map((attr) => {
        if (idAttCambio.toString() == attr.id.toString()) {
          attr.value = value
        }
        return attr;
      })

    })
  }

  handleOpciones(opcion,values,e) {

    if (opcion == "cancelar") {
      this.getElemento(this.state.id)
    }

    e.preventDefault();
  }

  removeLastGeoreferencia() {

    // elimino la última georeferencia del elemento
    this.setPosition(-1);

  }

  render() {
    return (
      <div className="elemento">

        <div className="elemento-herramientas no-print">
          <div className="btn-group-vertical">

            <button key="mapa-style-white" title="Quitar última posición del elemento" name="mapa-style-white" onClick={() => this.removeLastGeoreferencia()} style={{color:"red"}} className="btn btn-sm btn-light" type="button" >
              <i className="fa fa-eraser" aria-hidden="true"></i>
            </button>

          </div>
        </div>

        <form onSubmit={this.handleFormSubmit}>

          <div className="form-group">
            <label>Tipo de elemento</label>

            <Select
              placeholder={'Tipo elemento'}
              isMulti={false}
              name="elementoTipo"
              value={this.state.tipo}
              onChange={this.handleChangeSelectTipo}
              options={this.elementosTipos}
            />
            <small></small>

          </div>

          <div className="form-group">

            <label>Elemento padre</label>
            <Select
              placeholder={'Elemento padre'}
              isMulti={false}
              value={this.state.padre}
              onChange={this.handleChangeSelectPadre}
              options={this.elementos.map((e) => { return {...e,...{value:e.id,label:e.codigo} } } )}
            />

            <small></small>
          </div>

          <div className="form-group">
            <label>Nombre</label>
            <input value={this.state.nombre} onChange={(e) => this.handleChange(e)} name="nombre" type="text" className="form-control" placeholder="" />
            <small></small>
          </div>

          {this.state.atributos.map((atributo,index) => {
            return (
              <div key={index} className="form-group">
                <label>{atributo.nombre}</label>
                <input
                  onChange={(e) => this.handleChangeAtributo(e)}
                  data-id={atributo.id.toString()}
                  data-nombre={atributo.nombre}
                  name={"atr-"+atributo.id.toString()}
                  type="text"
                  className="form-control"
                  placeholder=""
                  value={this.state.atributos[index].value}
                />
                <small></small>

              </div>)
          })}

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
    this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='elementos-red' && e.tipoMarker!='elemento-red'))
    this.props.setPolylines(this.props.polylines.filter((e) => e.tipoPolyline!='elementos-red' && e.tipoPolyline!='elemento-red'))
  }
}
