import React, {Component} from 'react';
import { compose, withProps, withState, lifecycle, withHandlers } from "recompose"

import { withScriptjs, withGoogleMap, GoogleMap, Marker, Polyline } from "react-google-maps"
const { DrawingManager } = require("react-google-maps/lib/components/drawing/DrawingManager");
import MarkerClusterer from "react-google-maps/lib/components/addons/MarkerClusterer";
import SearchBox from "react-google-maps/lib/components/places/SearchBox";

import { styles } from './mapa/opciones.json'
import { Button, ButtonGroup } from 'reactstrap';

import {TipoMapa} from './mapa/herramientas/tipoMapa'
import {Conexiones} from './mapa/herramientas/conexiones'
const { MarkerWithLabel } = require("react-google-maps/lib/components/addons/MarkerWithLabel");

const _ = require("lodash");
//const google = window.google;

const MyMapComponent = compose(
  withProps({
    googleMapURL: "https://maps.googleapis.com/maps/api/js?key=AIzaSyA9PXrnxjAnpKBTkVZueV5dBzdVewxNy-o&v=3.exp&libraries=geometry,drawing,places",
    //googleMapURL: "https://maps.googleapis.com/maps/api/js?key=AIzaSyD0ltBbgLWM4jf9tVmf_-f9lHzg0-cmw4Q&v=3.exp&libraries=geometry,drawing,places",
    loadingElement: <div style={{ height: `100%` }} />,
    containerElement: <div id="Gmap" style={{ position:`absolute`,top:`0`,left:`0`, height: `100%`, width: `100%`, zIndex:`100` }} />,
    mapElement: <div style={{ height: `100%` }} />,
  }),
  withState('map', 'setMap', null),
  withState('searchBox', 'setSearchBox', null),
  withState('bounds', 'setBounds', null),
  withState('center', 'setCenter', null),
  withScriptjs,
  withGoogleMap,
  withHandlers(() => {

    return {
      onMapMounted: (props) => ref => {
        props.setMap(ref)
      },
      onSearchBoxMounted: (props) => ref => {
        props.setSearchBox(ref)
      },

      onZoomChanged: (props) => () => {
        props.onMapZoomChanged(props.map.getZoom())
      },

      onBoundsChanged: (props) => () => {
        props.setBounds(props.map.getBounds())
        props.setCenter(props.map.getCenter())
      },

      onPlacesChanged: (props) => () => {

        const places = props.searchBox.getPlaces();
        const bounds = new google.maps.LatLngBounds();

        places.forEach(place => {
          if (place.geometry.viewport) {
            bounds.union(place.geometry.viewport)
          } else {
            bounds.extend(place.geometry.location)
          }
        });

        const nextMarkers = places.map(place => ({
          position: place.geometry.location,
        }));

        const nextCenter = _.get(nextMarkers, '0.position', props.center);


        //props.map.setCenter(nextCenter)
        //props.map.setCenter(nextCenter)

        props.map.fitBounds(bounds);
      },
    }
  }),
  lifecycle({
    componentDidMount() {},
    componentWillReceiveProps(nextProps){},
    componentWillUpdate() {}
  }),
)((props) =>
  <GoogleMap

    center={props.defaultCenter ? props.defaultCenter : {lat:-28.0631595,lng:-67.5809792}}

    onClick={(e) => { return props.onMapClick(e)}}

    ref={props.onMapMounted}
    onBoundsChanged={props.onBoundsChanged}

    onZoomChanged={props.onZoomChanged}

    zoom={props.zoom ? props.zoom : 14}

    options={{
      zoomControl: true,
      mapTypeControl: false,
      scaleControl: false,
      streetViewControl: true,
      streetViewControlOptions: {
        position: google.maps.ControlPosition.TOP_LEFT
      },
      rotateControl: true,
      fullscreenControl: false,
      mapTypeId: props.mapTypeId,
      styles: props.mapaStyle
    }}

  >

    <div className="no-print">
        <SearchBox
          ref={props.onSearchBoxMounted}
          bounds={props.bounds}
          controlPosition={google.maps.ControlPosition.TOP_LEFT}
          onPlacesChanged={props.onPlacesChanged}
        >
          <input
            type="text"
            placeholder="Buscar dirección"
            style={{
              boxSizing: `border-box`,
              border: `1px solid transparent`,
              width: `300px`,
              height: `32px`,
              marginTop: `6px`,
              padding: `0 12px`,
              borderRadius: `3px`,
              boxShadow: `0 2px 6px rgba(0, 0, 0, 0.3)`,
              fontSize: `13px`,
              outline: `none`,
              textOverflow: `ellipses`,
            }}
          />
        </SearchBox>
    </div>
    {(props.redElementos.puntos.length > 0) && props.redElementos.puntos.map((punto,index) => {
      return (
          <Marker

            key={"M"+index+punto.id}


            position={{ lat: punto.lat, lng: punto.lng }}
            onClick={(e) => {props.onPuntoClick({"tipo":"redElemento", "datos": {id:punto.id}, "e":e})}}

            title={punto.nombre != ' ' ? punto.codigo +" - "+ punto.nombre : punto.codigo}
          />
        )
      })}

    {(props.redElementos.trazas.length > 0) && props.redElementos.trazas.map((trazo,index) => {
      return (
        <Polyline

          key={"P"+index+trazo.id}
          options={{strokeColor: '#'+trazo.color}}
          key={"t"+index}
          path={trazo.georeferencias}

          onClick={(e) => {props.onTrazaClick({"tipo":"redElemento", "datos":{id:trazo.id}, "e":e})}}

        />
      )
    })}

    {(props.redElementos.punto.lat != "") &&
      <Marker
        key={"M0"}

        icon={{
          fillColor:"#"+props.redElementos.punto.color,
          path:props.redElementos.punto.svgPath,
          scale: props.zoom != null ? ((props.zoom/2)*0.05) : 0,
          fillOpacity: 1,
        }}

        position={{ lat: props.redElementos.punto.lat, lng: props.redElementos.punto.lng }}
        onClick={(e) => {props.onPuntoClick(e)}}

      />
    }

    {(props.redElementos.traza.georeferencias != undefined && props.redElementos.traza.georeferencias.length > 0) &&
      <Polyline
        options={{strokeColor: '#'+props.redElementos.traza.color}}

        key="P0"
        path={props.redElementos.traza.georeferencias}
        onClick={(e) => {props.onTrazaClick(e)}}
      />
    }



   {(props.herramientaMapaActual == "filtro-conexiones") &&

      <DrawingManager

        onPolygonComplete={(pol) => {
          props.onHerramientasConexionesDrawingComplete(pol)
        }}

        options={{
          drawingControl: true,
          drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: [
              google.maps.drawing.OverlayType.POLYGON,
            ],
          },
          polygonOptions: {
            strokeWeight: 1,
            editable: false,
            fillColor: `#018BFE`,
            fillOpacity:.5
          },
        }}
      />
    }

    {(props.herramientaConexiones.puntos != undefined && props.herramientaConexiones.puntos.length > 0) &&
      <MarkerClusterer
        defaultZoomOnClick={true}
        minimumClusterSize={3}
        defaultMaxZoom={17}
        averageCenter
        styles={[{
          textColor: 'white',
          url: '../../../assets/imagenes/map/m1.png',
          height: 50,
          width: 50
        }]}
        enableRetinaIcons
        gridSize={60}
      >
      {props.herramientaConexiones.puntos.map((conexion) => {

        return (
          <MarkerWithLabel
              key={"conexiones-" + conexion.id}
              position={{ lat: parseFloat(conexion.latitud),lng: parseFloat(conexion.longitud) }}

              icon={{
                fillColor:"#018BFE",
                path:'M0,0h32v32H0V0z',
                scale: props.zoom != null ? ((props.zoom/2)*0.03) : 0,
                fillOpacity: 1,
              }}

              labelAnchor={new google.maps.Point(7, -8)}
              labelStyle={{backgroundColor: "#fff", color:"#018BFE", fontSize: "9px",  padding: "0px"}}

              title={"id conexión: " + conexion.id.toString()}
          >
            <div>
              {props.zoom > 13 ? conexion.tarifas.toString() : ""}
            </div>
          </MarkerWithLabel>
        )

      })}

      </MarkerClusterer>
    }

  </GoogleMap>

)

export class Map extends Component {

  state = {
    "mapaStyle": styles.white,
    "mapTypeId":"roadmap",
    "herramientaConexiones":{
      "puntos": [],
      "drawing": null,
      "cantidad": []
    }
  }



  handleMapaStyle = (mapaStyle) => {
    this.setState({
      'mapaStyle':mapaStyle
    })
  }

  handleMapaTypeId = (mapTypeId) => {
    this.setState({
      'mapTypeId':mapTypeId
    })
  }

  onHerramientasConexionesDrawingComplete = (drawing) => {
    this.setState({
      "herramientaConexiones": {
        "puntos": this.state.herramientaConexiones.puntos,
        "drawing": drawing,
        "cantidad": this.state.herramientaConexiones.cantidad
      }
    })
  }

  handleHerramientaConexiones = (herramientaConexiones) => {
    this.setState({
      'herramientaConexiones':herramientaConexiones
    })
  }

  shouldComponentUpdate(nextProps, nextState) {
    return false;
  }

  render() {

    return (
      <div style={{ position: `absolute`, width:`100%`, zIndex:`20`, height:`100%` }}>

        <MyMapComponent

          opcionMenuActual={this.props.opcionMenuActual}

          mapaStyle = {this.state.mapaStyle}
          mapTypeId = {this.state.mapTypeId}

          zoom={this.props.zoom}
          defaultCenter= {this.props.defaultCenter}

          onMapZoomChanged={(zoom) => { this.props.onMapZoom(zoom) }}

          onMapClick={(e) => {this.props.onMapClick(e)}}
          onPuntoClick={this.props.onPuntoClick}
          onTrazaClick={this.props.onTrazaClick}

          //diseño red
          redElementos={this.props.redElementos}

          //herramientas mapa
          herramientaMapaActual = {this.props.herramientaMapaActual}

          //herramientas mapa > conexiones
          herramientaConexiones={this.state.herramientaConexiones}
          onHerramientasConexionesDrawingComplete={this.onHerramientasConexionesDrawingComplete}

        />

        <div className="mapa-herramientas no-print">

            <TipoMapa
              handleMapaStyle={this.handleMapaStyle}
              handleMapaTypeId={this.handleMapaTypeId}

              handleHerramientaMapaActual={this.props.handleHerramientaMapaActual}
              herramientaMapaActual = {this.props.herramientaMapaActual}
            />

            <hr/>

            <Conexiones
              host={this.props.host}
              baseUrl={this.props.baseUrl}
              pathInfo={this.props.pathInfo}
              uri={this.props.uri}
              usuarioHash={this.props.usuarioHash}

              handleMapaStyle={this.handleMapaStyle}
              handleMapaTypeId={this.handleMapaTypeId}

              handleHerramientaMapaActual={this.props.handleHerramientaMapaActual}
              herramientaMapaActual = {this.props.herramientaMapaActual}

              herramientaConexiones={this.state.herramientaConexiones}
              handleHerramientaConexiones={this.handleHerramientaConexiones}
            />

        </div>

        <div style={{position:"absolute", zIndex:"500", bottom:"10px", left:"10px",width:"50%", backgroundColor:"#fff"}}>

          {this.props.redElementos && this.props.redElementos.cantidad != undefined &&  this.props.redElementos.cantidad.map((cantidad) => {
            return (<div key={cantidad.nombre} style={{marginLeft:"8px", paddingTop:"2px", paddingBottom:"2px", paddingLeft:"8px", borderLeft:"1px #cccc solid", float: "left"}}>
              <b>{cantidad.nombre}:</b>{cantidad.total} {cantidad.unidad}
            </div>)
          })}

          {this.state.herramientaConexiones.cantidad != undefined &&  this.state.herramientaConexiones.cantidad.map((cantidad) => {
            return (<div key={cantidad.nombre} style={{marginLeft:"8px", paddingTop:"2px", paddingBottom:"2px", paddingLeft:"8px", borderLeft:"1px #cccc solid", float: "left"}}>
              <b>{cantidad.nombre}:</b>{cantidad.total}
            </div>)
          })}

        </div>

      </div>
    )
  }

}
