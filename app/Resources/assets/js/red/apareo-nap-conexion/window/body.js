import React, {Component} from 'react';
import {Apareo} from './body/apareo.js'

export class Body extends Component {

  _render(opcionMenuActual) {

    switch (opcionMenuActual.id) {
      default:
        return <Apareo
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
