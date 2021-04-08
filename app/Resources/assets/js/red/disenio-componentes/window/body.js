import React, {Component} from 'react';
import {Elemento} from './body/elemento.js'
import {ElementoListado} from './body/elemento-listado.js'

export class Body extends Component {

  _render(opcionMenuActual) {

    switch (opcionMenuActual.id) {

      case "elemento-nuevo":

          return <Elemento
                  {...this.props}
                  elementoId = {0}
                 />
        break;

        case "elemento-editar":
            return <Elemento
                    {...this.props}
                    elementoId = {opcionMenuActual.params.id}
                   />
          break;

        default:
          return <ElementoListado
                  {...this.props}
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
