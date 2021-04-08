import React, {Component} from 'react';

export class TareaListado extends Component {

  constructor(props) {
    super(props)

    this.state = {
      "id": props.elementoId == undefined || props.elementoId == null ? 0 : props.elementoId,
      "obra": null,
      "load": 0,
      "elementos": []
    }
  }


  componentWillMount() {
    this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='gestion-obra'))
    this.getObra(this.props.obraId)
  }

  getObra = (id = 0) => {

    let url = this.props.uri + "obra/"+id
    fetch(url)
    .then(res => res.json())
    .then(data => {

      if(data.obra.id != undefined) {

        this.setState({
          "id":parseInt(data.obra.id),
          "obra":data.obra,
          "elementos": data.obra.elementos.length>0 ? data.obra.elementos : [],
          "load": this.state.load + 1,
        }, () => {
          this.setMarkers()
        })

      }

    })

  }

  setMarkers() {

    let p = this.state.elementos.map((e) => {
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
    });

    let promise = this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='gestion-obra' && e.tipoMarker!='elementos-red'))
    promise.then((success) => {
      let m = this.props.markers.concat(p)
      m = Array.from(new Set(m.map(JSON.stringify))).map(JSON.parse);
      this.props.setMarkers(m)
    })

  }

  render() {

    if (this.state.obra != null) {


      return (

      <div style={{position:'absolute',height: '85%', width: '100%', overflowY: 'auto'}}  >

        <div className="items" >
          {this.state.obra==null || this.state.obra.tareas.length==0 && "No hay tareas para esta obra"}
          {this.state.obra!=null && this.state.obra.tareas.length>0 && this.state.obra.tareas.map((t) => {
            return (

              <div  className="row" style={{margin:"2px 0px 0px 2px", padding:"8px 2px",background:"#f8f8f8",borderLeft:"solid 5px #ccc"}} key={t.id}>

                <div className="col-10">
                  <b>#{t.id} - {t.tipo.label} </b> <br/>
                  {t.cuadrilla.nombre} <br/>
                  <span style={{color:"#666"}}>
                    {t.fechaFormat} &nbsp;&nbsp; Avance {t.avance} %
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
                        onClick={()=>{ this.props.handleOpcionMenuTareaActual({"id": "tarea-editar","params":{"id":t.id}}) }}
                      >
                          Editar
                      </a>
                    </div>
                  </div>

                </div>

              </div>
            )
          })}

        </div>

      </div>
      );
    }

    return null
  }

  componentWillUnmount() {
    // quito los elementos del mapa de esta vista elementos de red en el mapa
    this.props.setMarkers(this.props.markers.filter((e) => e.tipoMarker!='gestion-obra'))
  }

}
