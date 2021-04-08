import React, {Component} from 'react';

export class ObraListado extends Component {

  searchSetTimeOut = null;

  state = {
    "obras": [],
    "elementosSinObra": [],
    "elementos": [],
    "elementosLoad": 1,
    "searchText": "",
    "searchNombre":1,
    "searchElemento":1,
  }

  componentWillMount() {

    this.props.handleHerramientaMapaActual("");
    this.getObras()
    this.getElementosRedSinObra()
  }

  getObras = () => {

    this.setState({
      "elementosLoad": 1
    })

    let url = this.props.uri + "obras?n="+this.state.searchNombre+"&s="+this.state.searchText+"&e="+this.state.searchElemento

    fetch(url)
    .then(res => res.json())
    .then(data => {

      let op = data.obras.filter(o => o.elementos.length>0)

      let elementos = []
      op.map((o) => o.elementos.map(e => elementos.push(e)))


      this.setState({
        "obras": data.obras,
        "elementos": elementos,
        "elementosLoad": 0
      }, () => {
        this.setMarkers()
      })

      this.searchSetTimeOut = null;

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
        "tipoMarker":"gestion-obra"
      }
    }));

    let promise = this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='gestion-obra'))
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

  handleDropdownOpciones(opcion, values) {

    if (opcion == "ver") {
      this.props.handleOpcionMenuActual({
        "id": "obra-editar",
        "label": "Editar elemento",
        "icon": "Editar",
        "params":values.params
      })
    }

    if (opcion == "body-tarea") {
      this.props.handleOpcionMenuActual({
        "id": "body-tarea",
        "label": "Tarea listado",
        "icon": "Listado",
        "params":values.params
      })
    }

  }

  changeOptionsSearch() {

    // para evitar que se inicien varias busquedas
    if (this.searchSetTimeOut != null) {clearTimeout(this.searchSetTimeOut)}
    this.searchSetTimeOut = setTimeout(() => { this.getObras(); }, 1500);

  }

  render() {

    return (
      <div className="listado" >

        <div>

          <div className="input-group">
            <input readOnly={this.state.elementosLoad ? true : false} style={{padding:'2px', height:"25px", fontSize:"12px", borderRadius:0}} name="searchText" onChange={(e) => {this.setState({"searchText": e.target.value}); this.changeOptionsSearch() }} value={this.state.searchText} type="text" className="form-control" placeholder="Buscar obra" />

            <div className="input-group-addon" id="basic-addon2" style={{ padding:0, borderRadius:0}}>

               <div className="btn-group-toggle" data-toggle="buttons" style={{width:"45px", padding:0, borderRadius:0}}>

                <button key="sn" disabled={this.state.elementosLoad ? true : false} onClick={(e) => { this.setState({"searchNombre": e.target.value == 1 ? 0 : 1}); this.changeOptionsSearch() }} name="searchNombre" value={this.state.searchNombre} title={"Buscar "+this.state.searchText+" en nombre de obra"} className="btn btn-sm btn-primary active" style={{lineHeight:1, minWidth:"15px", padding:"4px 5px", margin:0, borderRadius:0}}>
                  <input type="checkbox" defaultChecked={true} /> N
                </button>

                <button key="sc" disabled={this.state.elementosLoad ? true : false} onClick={(e) => {this.setState({"searchElemento": e.target.value == 1  ? 0 : 1}); this.changeOptionsSearch() }} name="searchElemento" value={this.state.searchElemento} title={"Buscar "+this.state.searchText+" en elementos de red"} className="btn btn-sm btn-primary active"  style={{lineHeight:1, minWidth:"15px", padding:"4px 5px", margin:0, borderRadius:0}}>
                  <input type="checkbox" defaultChecked={true} /> E
                </button>

               </div>

            </div>

          </div>
        </div>

        <div className="items" >
          {this.state.obras.map((obra) => {
              return (


                <div  className="row" style={{margin:"2px 0px 0px 2px", padding:"8px 2px",background:"#f8f8f8",borderLeft:"solid 5px #ccc"}} key={obra.id}>
                  <div className="col-10">
                      {obra.nombre} <br/>
                    <span style={{color:"#666", fontSize:"12px"}}>
                        Empalmados {obra.avance} % - Tareas {obra.avanceTarea} de {obra.tareas}
                      </span>
                  </div>

                  <div className="col-1" >

                    <div className="dropdown mr-1" >
                      <a
                        href="javascript:void(0)"
                        title="Más opciones"
                        className="dropdown-toggle  text-right"
                        data-toggle="dropdown">
                        <i className="fa fa-ellipsis-h" aria-hidden="true"></i>
                      </a>
                      <div className="dropdown-menu dropdown-menu-right">
                        <a className="dropdown-item"
                          href="javascript:void(0)"
                          onClick={() => this.handleDropdownOpciones("ver", {'params': {'id': obra.id}})}
                        >
                            Editar
                        </a>
                        <a className="dropdown-item"
                          href="javascript:void(0)"
                          onClick={() => this.handleDropdownOpciones("body-tarea", {'params': {'id': obra.id}})}
                        >
                            Tareas
                        </a>
                      </div>
                    </div>

                  </div>

                </div>
              )
          })}
        </div>
      </div>
    )
  }

  componentWillUnmount() {
    // quito los elementos del mapa de esta vista elementos de red en el mapa
    this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='gestion-obra'))
  }

}
