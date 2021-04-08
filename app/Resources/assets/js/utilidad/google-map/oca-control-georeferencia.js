import React, { Component, PureComponent } from 'react';
import {render} from 'react-dom';
import {Map} from './oca-control-georeferencia/Map';
import 'normalize.css';

require('es6-promise').polyfill();
require('isomorphic-fetch');

class OcaControlGeoreferencia extends React.PureComponent {

  constructor(props){
    super(props)
    this.state = {
      offset: 0,
      href: this.props.href,
      markers: [],
      cnxCargadas: [],
      cargando: 1
    }
  }

  getMarkers() {

    let url = this.state.href.replace("*offset*",this.state.offset.toString());
    fetch(url)
      .then(res => res.json())
      .then(data => {

        if (data.conexiones.length > 0 ) {

          ///PARA EVITAR QUE SE REPITAN LOS ID DE CONEXION SI ES QUE SE REPITEN
          let cnxParaMostrar = []
          var cnxCargadas = [];

          cnxCargadas = this.state.markers.map((marker) => {
            return marker.id
          })

          for(var i=0; i < data.conexiones.length; ++i) {
            if(cnxCargadas.indexOf(data.conexiones[i].id) == -1) {
              cnxCargadas.push(data.conexiones[i].id)
              cnxParaMostrar.push(data.conexiones[i])
            }
          }
          //////

          this.setState({
            markers: this.state.markers.concat(cnxParaMostrar),
            offset:  data.offset
          });


        } else {

          this.setState({
            cargando: 0,
          })

        }
      });
  }

  componentDidMount() {
    this.getMarkers()
  }

  componentDidUpdate(nextProps, nextState){
    this.getMarkers()
  }

  render () {
    return (
      <div className="app-contenedor">
        <Map markers={this.state.markers} cargando={this.state.cargando} />
        {this.state.cargando == 1 ? <div className="cargando">CARGANDO CONEXIONES</div> : <div></div>}
      </div>
    );
  }
}

var rootElement = document.getElementById('oca-control-georeferencia');
render(<OcaControlGeoreferencia href={rootElement.getAttribute('data-href')} />, rootElement);
