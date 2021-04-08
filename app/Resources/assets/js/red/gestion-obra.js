import React from 'react';
import {render} from 'react-dom';
//import {Map} from './gestion-obra-componentes/map';

import {Map} from '../maps/map';

import {Window} from './gestion-obra-componentes/window';
import opciones from './gestion-obra-componentes/window/menu/opciones.json'

class App extends React.Component {

  state = {
    "markers": [],
    "polylines": [],
    "mapClick": null,
    "markerClick": null,
    "mapZoom": 14,
    "dimensionMarker": 16,
    "defaultCenter": {lat:-28.0625818,lng:-67.60109307},
    "opcionMenuActual":opciones.opciones[0],
    "herramientaMapaActual":[],
    "consumidorHerramientaMapaActual":null,
    "drawingManagerPolygonComplete":null,
  }

  handleOpcionMenuActual = (opcion) => {
    this.setState({
      "opcionMenuActual": opcion
    })
  }

  handleHerramientaMapaActual = (opcion) => {
    this.setState({
      "herramientaMapaActual": opcion
    })
  }

  handleConsumidorHerramientaMapaActual = (opcion) => {
    this.setState({
      "consumidorHerramientaMapaActual": opcion
    })
  }

  handleMapDefaultCenter = (defaultCenter) => {

    this.setState({
      "defaultCenter": defaultCenter
    })
  }

  onMapClick = (e) => {
    this.setState({
      "mapClick": e
    })
  }

  onMapZoom = (zoom) => {
    this.setState({
      "mapZoom": zoom
    })
  }

  onDrawingManagerPolygonComplete = (drawingManagerPolygonComplete) => {
    this.setState({
      "drawingManagerPolygonComplete": drawingManagerPolygonComplete
    })
  }

  onMarkerClick = (markerClick) => {
    this.setState({
      "markerClick": markerClick
    })
  }

  onPolylineClick = (params) => {}

  setMarkers = (markers) => {
    return new Promise((resolve, reject) => {
      this.setState({"markers": markers},() => {
        resolve(this.state.markers)
      })
    })
  }

  render () {
    return (
        <div className="app-contenedor">
          <Map

              {...this.props}

              markers={this.state.markers}
              setMarkers={this.setMarkers}

              polylines={this.state.polylines}

              mapZoom={this.state.mapZoom}
              defaultCenter= {this.state.defaultCenter}

              onMapZoom={this.onMapZoom}
              onMapClick={this.onMapClick}
              onMarkerClick={this.onMarkerClick}

              onDrawingManagerPolygonComplete = {this.onDrawingManagerPolygonComplete}
              drawingManagerPolygonComplete={this.state.drawingManagerPolygonComplete}

              handleHerramientaMapaActual={this.handleHerramientaMapaActual}

              handleConsumidorHerramientaMapaActual={this.handleConsumidorHerramientaMapaActual}
              consumidorHerramientaMapaActual={this.state.consumidorHerramientaMapaActual}

              herramientaMapaActual = {this.state.herramientaMapaActual}
              opcionMenuActual = {this.state.opcionMenuActual}
          />
          <Window

              {...this.props}

              onMapZoom={this.onMapZoom}

              handleOpcionMenuActual={this.handleOpcionMenuActual}
              opcionMenuActual = {this.state.opcionMenuActual}
              dimensionMarker = {this.state.dimensionMarker}

              handleHerramientaMapaActual={this.handleHerramientaMapaActual}
              herramientaMapaActual = {this.state.herramientaMapaActual}

              handleConsumidorHerramientaMapaActual={this.handleConsumidorHerramientaMapaActual}
              consumidorHerramientaMapaActual={this.state.consumidorHerramientaMapaActual}

              handleMapDefaultCenter={this.handleMapDefaultCenter}

              mapClick={this.state.mapClick}
              mapaZoom={this.state.mapZoom}
              defaultCenter={this.state.defaultCenter}
              drawingManagerPolygonComplete={this.state.drawingManagerPolygonComplete}

              markers={this.state.markers}
              markerClick={this.state.markerClick}
              setMarkers={this.setMarkers}


              polylines={this.state.polylines}
              setPolylines={(polylines) => {this.setState({"polylines": polylines})}}
          />
        </div>
    );
  }
}

var root = document.getElementById('app')
var scheme = root.getAttribute('scheme')
var host = root.getAttribute('host')
var baseUrl = root.getAttribute('baseUrl')
var pathInfo = root.getAttribute('pathInfo')
var uri = root.getAttribute('uri')
var usuarioHash = root.getAttribute('usuarioHash')

render(<App host={host} baseUrl={baseUrl} scheme={scheme} pathInfo={pathInfo} uri={uri} usuarioHash={usuarioHash}/>, root);
