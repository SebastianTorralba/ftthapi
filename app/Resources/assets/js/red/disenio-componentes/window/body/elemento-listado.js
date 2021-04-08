import React, {Component} from 'react';
require('es6-promise').polyfill();
require('isomorphic-fetch');
import Pagination from "react-js-pagination";

export class ElementoListado extends Component {

  searchSetTimeOut = null;
  itemsPorPagina = 14

  state = {
    "elementos": [],
    "elementosLoad": 1,
    "searchText": "",
    "searchNombre": 1,
    "searchCodigo": 1,
    "searchTipo": 1,
    "activePage": 1
  }

  componentDidMount() {
    this.props.onMapZoom(14)
    this.getElementos()
  }

  getElementos = () => {

    this.setState({
      "elementosLoad": 1
    })

    let url = this.props.uri + "elementos?s="+this.state.searchText+"&c="+this.state.searchCodigo+"&t="+this.state.searchTipo+"&n="+this.state.searchNombre

    fetch(url)
    .then(res => res.json())
    .then(data => {

      this.setState({
        "elementos": data.elementos,
        "elementosLoad": 0
      }, () => {
        this.setMarkers()
      })

    })

  }

  handleDropdownOpciones(opcion, values) {

    if (opcion == "ver") {
      this.props.handleOpcionMenuActual({
        "id": "elemento-editar",
        "label": "Editar elemento",
        "icon": "Editar",
        "params":values.params
      })
    }

  }

  handleMapDefaultCenter(lat, lng) {
    this.props.onMapZoom(21)
    this.props.handleMapDefaultCenter({lat:parseFloat(lat), lng:parseFloat(lng)})
  }

  handleChange = (e) => {

    this.setState({
      [e.target.name]:e.target.value
    })

    if (e.target.name=="searchText") {
      this.changeOptionsSearch()
    }
  }

  handleClick = (e) => {

    if (e.target.name == "searchTipo" || e.target.name == "searchCodigo" || e.target.name == "searchNombre") {
      this.setState({
        [e.target.name]:e.target.value == 0 ? 1 : 0
      })

      this.changeOptionsSearch()
    }
  }

  changeOptionsSearch() {

    // para evitar que se inicien varias busquedas
    if (this.searchSetTimeOut != null) {clearTimeout(this.searchSetTimeOut)}
    this.setState({activePage: 1});
    this.searchSetTimeOut = setTimeout(() => { this.getElementos(); }, 1000);
  }

  setMarkers() {

    let p = this.state.elementos.filter((e) => {
      return e.elementoTipo.tipoGeoreferencia == "punto" && e.georeferencias && e.georeferencias.length > 0;
    }).map((e) => {

      return {
        "id":e.id,
        "visible": this.props.mapZoom > 17 ? 1 : 0,
        "lat":parseFloat(e.georeferencias[0].latitud),
        "lng":parseFloat(e.georeferencias[0].longitud),
        "image": '/uploads/imagenes/tipo-elemento/'+e.elementoTipo.id+"_"+e.estadoActual.estado+"_"+this.props.dimensionMarker+".png",
        "opacity":1,
        "obra_id":0,
        "title":e.codigo+" - "+e.nombre+" - "+e.estadoActual.estado,
        "groupBy": e.lvl > 0 ? e.parent.id : null,
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

    let promise = this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='elementos-red'))
    promise.then((success) => {
      let m = this.props.markers.concat(p)
      m = Array.from(new Set(m.map(JSON.stringify))).map(JSON.parse);
      this.props.setMarkers(m)
    })

  }

  handlePageChange = (pageNumber) => {
    this.setState({activePage: pageNumber});
  }

  render() {

    const background = ["#9e9e9e","#bdbdbd","#e0e0e0","#eeeeee","#f5f5f5","#fafafa","#fff","#fff","#fff","#fff","#fff","#fff"]
    const color = ["#fff","#fff","#666","#666","#666","#666","#333","#333","#333","#333","#333","#333"]

    let paginaDesde = (this.state.activePage-1) * this.itemsPorPagina
    let paginaHasta = (this.state.activePage * this.itemsPorPagina) - 1

    return (
      <div className="listado">

        <div>

          <div className="input-group">
            <input readOnly={this.state.elementosLoad ? true : false} style={{padding:'2px', height:"25px", fontSize:"12px", borderRadius:0}} name="searchText" onChange={(e) => this.handleChange(e)} value={this.state.searchText} type="text" className="form-control" placeholder="Buscar elemento" />

            <div className="input-group-addon" id="basic-addon2" style={{ padding:0, borderRadius:0}}>

               <div className="btn-group-toggle" data-toggle="buttons" style={{width:"65px", padding:0, borderRadius:0}}>

                <button key="sn" disabled={this.state.elementosLoad ? true : false} onClick={(e) => {this.handleClick(e)}} name="searchNombre" value={this.state.searchNombre} title={"Buscar "+this.state.searchText+" en nombre"} className="btn btn-sm btn-primary active" style={{lineHeight:1, minWidth:"15px", padding:"4px 5px", margin:0, borderRadius:0}}>
                  <input type="checkbox" defaultChecked={true} /> N
                </button>

                <button key="sc" disabled={this.state.elementosLoad ? true : false} onClick={(e) => {this.handleClick(e)}} name="searchCodigo" value={this.state.searchCodigo} title={"Buscar "+this.state.searchText+" en código"} className="btn btn-sm btn-primary active"  style={{lineHeight:1, minWidth:"15px", padding:"4px 5px", margin:0, borderRadius:0}}>
                  <input type="checkbox" defaultChecked={true} /> C
                </button>

                <button key="st" disabled={this.state.elementosLoad ? true : false} onClick={(e) => {this.handleClick(e)}} name="searchTipo" value={this.state.searchTipo} title={"Buscar "+this.state.searchText+" en tipo elemento"} className="btn btn-sm btn-primary active"  style={{lineHeight:1, minWidth:"15px", padding:"4px 5px", margin:0, borderRadius:0}}>
                  <input type="checkbox" defaultChecked={true} />  T
                </button>

               </div>

            </div>

          </div>
        </div>

        <div className="items" >

            {this.state.elementos.filter((e,index) => { return index>=paginaDesde && index<=paginaHasta; }).map((elemento) => {

              return (
                <div key={elemento.id} className="row" style={{margin:"2px 0px 0px "+(elemento.lvl*4)+"px",color:color[elemento.lvl],background:background[elemento.lvl],borderLeft:"solid 5px #"+elemento.elementoTipo.colorHexa}} >

                  <div className="col-10" >
                    <div className="row">

                      <div className="col-12">

                        <a href="javascript:void(0)" onClick={() => { this.handleMapDefaultCenter(elemento.lastGeoreferencias.lat, elemento.lastGeoreferencias.lng)  }} style={{color:color[elemento.lvl]}} className="title" >
                          {elemento.codigo}
                        </a>

                      </div>

                      <div className="col-12" >
                        <span className="referencia">
                          {"L"+(elemento.lvl+1)}
                        </span> &nbsp;/&nbsp;
                        <span className="referencia badge">
                          {elemento.elementoTipo.nombre}
                        </span>
                      </div>

                    </div>
                  </div>

                  <div className="col-1" >

                    <div className="dropdown mr-1" >
                      <a
                        style={{color:color[elemento.lvl]}}
                        href="javascript:void(0)"
                        title="Más opciones"
                        className="dropdown-toggle  text-right"
                        data-toggle="dropdown">
                        <i className="fa fa-ellipsis-h" aria-hidden="true"></i>
                      </a>
                      <div className="dropdown-menu dropdown-menu-right">
                        <a className="dropdown-item"
                          href="javascript:void(0)"
                          onClick={() => this.handleDropdownOpciones("ver", {'params': {'id': elemento.id }})}
                        >
                            Ver
                        </a>
                      </div>
                    </div>

                  </div>

                </div>
              )
            })}

            {this.state.elementos.length > this.itemsPorPagina ?
              <div>
                <Pagination
                  activePage={this.state.activePage}
                  itemsCountPerPage={this.itemsPorPagina}
                  totalItemsCount={this.state.elementos.length}
                  pageRangeDisplayed={4}
                  onChange={this.handlePageChange}
                  />
              </div>
            :
              null
            }
        </div>
      </div>
    )
  }

  componentWillUnmount() {
    // quito los elementos del mapa de esta vista elementos de red en el mapa
    this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='elementos-red'))
  }
}
