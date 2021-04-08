import React, {Component} from 'react';
import {Tarea} from './body-tarea/tarea.js'
import {TareaListado} from './body-tarea/tarea-listado.js'

export class BodyTarea extends Component {

  constructor(props) {
    super(props)

    this.state = {
      "opcionMenuTareaActual": {"id": "tarea-listado","params":{"id":0}},
      "obra": null,
      "elementos": [],
      "load": 0,
    }
  }

  componentWillMount() {

    // inicializo elementos de red en el mapa
    this.getObra(this.props.obraId)
  }

  getObra = (id = 0) => {

    let url = this.props.uri + "obra/"+id
    fetch(url)
    .then(res => res.json())
    .then(data => {

      if(data.obra.id != undefined) {

        this.setState({
          "obra":data.obra,
          "elementos": data.obra.elementos.length>0 ? data.obra.elementos : [],
          "load": this.state.load + 1,
        })

      }

    })

  }

  _render(opcionMenuTareaActual) {

    switch (opcionMenuTareaActual.id) {

      case "tarea-nueva":
          return <Tarea
                    {...this.props}
                    elementoId={0}
                    handleOpcionMenuTareaActual={this.handleOpcionMenuTareaActual}
                 />
        break;

        case "tarea-editar":
            return <Tarea
                    {...this.props}
                    elementoId={opcionMenuTareaActual.params.id}
                    handleOpcionMenuTareaActual={this.handleOpcionMenuTareaActual}
                   />
          break;

        default:
          return <TareaListado
                    {...this.props}
                    handleOpcionMenuTareaActual={this.handleOpcionMenuTareaActual}
                 />
    }
  }

  handleOpcionMenuTareaActual= (opcionActual) => {
    this.setState({
      "opcionMenuTareaActual":opcionActual
    })
  }

  render() {

    if (this.state.obra != null) {
      return (
        <div>

          <h6 style={{padding:"5px", margin:"0px"}}>

            {this.state.obra.nombre}
          </h6>

          <div className="row">
            <div className="col-12 sub-menu">

              <button
                disabled={this.state.opcionMenuTareaActual.id == "tarea-listado" ? true : false}
                style={{marginLeft:"10px"}}
                onClick={()=>{ this.handleOpcionMenuTareaActual({"id": "tarea-listado","params":{"id":0}}) }}
              >
                Listar tareas
              </button>

              <button
                disabled={this.state.opcionMenuTareaActual.id == "tarea-nueva" ? true : false}
                style={{marginLeft:"10px"}}
                onClick={()=>{ this.handleOpcionMenuTareaActual({"id": "tarea-nueva","params":{"id":0}}) }}
              >
                Nueva tarea
              </button>

            </div>
          </div>

          {this._render(this.state.opcionMenuTareaActual)}

        </div>
      )
    }

    return null
  }
}
