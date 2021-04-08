import React, {Component} from 'react';
require('es6-promise').polyfill();
require('isomorphic-fetch');

export class Atributo extends Component {

  state={
    "atributos": []
  }

  componentWillMount() {
    this.getAtributos()
  }

  getAtributos = () => {

    let url = this.props.host + "/red/topologia/get-atributos"

    fetch(url)
    .then(res => res.json())
    .then(data => {
        this.setState({
          "atributos": data.atributos.length > 0 ? data.atributos : [],
        })
    })
  }

  render() {
    return (
          {this.state.atributos.map((atributo) => {
            <div className="form-group">
              <label></label>
              <input onChange={(e) => this.handleChange(e)} name="elementoNombre" type="text" className="form-control" placeholder="" />
              <small></small>
            </div>}
          )}
    )
  }
}
