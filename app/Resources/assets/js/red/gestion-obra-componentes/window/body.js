import React, {Component} from 'react';
import {Obra} from './body/obra.js'
import {ObraListado} from './body/obra-listado.js'
import {BodyTarea} from './body/body-tarea.js'

export class Body extends Component {

  _render(opcionMenuActual) {

    switch (opcionMenuActual.id) {

      case "obra-nuevo":

          return <Obra
                    {...this.props}
                    elementoId = {0}
                 />
        break;

        case "obra-editar":
            return <Obra
                    {...this.props}
                    elementoId = {opcionMenuActual.params.id}
                   />
          break;

          case "body-tarea":
              return <BodyTarea
                      {...this.props}
                      obraId = {opcionMenuActual.params.id}
                     />
            break;

        default:
               return <ObraListado
                    {...this.props}
                    elementoId = {0}
                  />
    }
  }

  render() {
    return (
      <div className="body">
        {this._render(this.props.opcionMenuActual)}
      </div>
    )
  }
}
