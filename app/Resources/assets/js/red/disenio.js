import React from 'react';
import {render} from 'react-dom';
import {Map} from '../maps/map';
import {Window} from './disenio-componentes/window';
import opciones from './disenio-componentes/window/menu/opciones.json'

class App extends React.Component {

  state = {
    "markers": [],
    "polylines": [],
    "mapClick": null,
    "markerClick": null,
    "plylineClick": null,
    "mapZoom": 14,
    "dimensionMarker": 16,
    "defaultCenter": {lat:-28.0625818,lng:-67.6010937},
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

  onPolylineClick = (polylineClick) => {
    this.setState({
      "polylineClick": polylineClick
    })
  }

  setMarkers = (markers) => {
    return new Promise((resolve, reject) => {
      this.setState({"markers": markers},() => {
        resolve(this.state.markers)
      })
    })
  }

  setPolylines = (polylines) => {
    return new Promise((resolve, reject) => {
      this.setState({"polylines": polylines},() => {
        resolve(this.state.polylines)
      })
    })
  }

  getUri = (recurso = '', segura = 1) => {
    let { uri, usuarioHash } = this.props;

    if (segura) {
      uri = uri + usuarioHash + "/";
    }

    if (recurso !== '') {
      uri = uri + recurso;
    }

    return uri;
  }

  render () {
    return (
        <div className="app-contenedor">

          <Map

              {...this.props}

              markers={this.state.markers}
              setMarkers={this.setMarkers}
              onMarkerClick={this.onMarkerClick}

              polylines={this.state.polylines}
              setPolylines={this.setPolylines}
              onPolylineClick={this.onPolylineClick}


              mapZoom={this.state.mapZoom}
              defaultCenter= {this.state.defaultCenter}

              onMapZoom={this.onMapZoom}
              onMapClick={this.onMapClick}

              onDrawingManagerPolygonComplete = {this.onDrawingManagerPolygonComplete}
              drawingManagerPolygonComplete={this.state.drawingManagerPolygonComplete}

              handleConsumidorHerramientaMapaActual={this.handleConsumidorHerramientaMapaActual}
              consumidorHerramientaMapaActual={this.state.consumidorHerramientaMapaActual}

              handleHerramientaMapaActual={this.handleHerramientaMapaActual}
              herramientaMapaActual = {this.state.herramientaMapaActual}

              opcionMenuActual = {this.state.opcionMenuActual}
          />



          <Window

             {...this.props}
             getUri={this.getUri}

             dimensionMarker={this.state.dimensionMarker}

             markers={this.state.markers}
             setMarkers={this.setMarkers}
             markerClick={this.state.markerClick}
             onMarkerClick={this.onMarkerClick}

             polylines={this.state.polylines}
             setPolylines={this.setPolylines}
             polylineClick={this.state.polylineClick}
             onPolylineClick={this.onPolylineClick}

             handleMapDefaultCenter={this.handleMapDefaultCenter}
             defaultCenter={this.state.defaultCenter}

             onMapZoom={this.onMapZoom}
             mapZoom={this.state.mapZoom}

             onMapClick={this.onMapClick}
             mapClick={this.state.mapClick}



             onDrawingManagerPolygonComplete = {this.onDrawingManagerPolygonComplete}
             drawingManagerPolygonComplete={this.state.drawingManagerPolygonComplete}


             handleConsumidorHerramientaMapaActual={this.handleConsumidorHerramientaMapaActual}
             consumidorHerramientaMapaActual={this.state.consumidorHerramientaMapaActual}

             handleHerramientaMapaActual={this.handleHerramientaMapaActual}
             herramientaMapaActual={this.state.herramientaMapaActual}

             handleOpcionMenuActual={this.handleOpcionMenuActual}
             opcionMenuActual={this.state.opcionMenuActual}
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
