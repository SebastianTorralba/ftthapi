import React, {Component} from 'react';
import opciones from './menu/opciones.json'

const Button = (props) => (
  <button onClick={props.onClick} >{props.label}</button>
)

export class Menu extends Component {

  constructor(props) {
    super(props)
    this.state = {
      opcionSeleccionada: props.opcionMenuActual
    }
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.opcionMenuActual.id != "imprimir") {
      this.setState({
        'opcionSeleccionada': nextProps.opcionMenuActual.id
      })
    }
  }

  handleClick(opcion) {

    if (opcion.id != "imprimir") {
      this.setState({opcionSeleccionada:opcion.id})
    }

    if (opcion.id == "imprimir") {
      window.print()
    }
  }

  _renderButton(opcion) {

    const disabled = opcion.id===this.state.opcionSeleccionada ? true : false;

    return (
      <button
        key={opcion.id}
        disabled={disabled}
        title={opcion.label}
        onClick={() => {
          this.props.handleOpcionMenuActual(opcion)
          this.handleClick(opcion)
      }}>
        {opcion.icon}
      </button>
    )
  }

  render() {

    return (
      <div className="menu">
        {opciones.opciones.map((opcion) => {
          return this._renderButton(opcion)
        })}
      </div>
    )
  }
}
