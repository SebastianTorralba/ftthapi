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

    {(props.markers.length > 0) && props.markers.filter(p => p.visible == 1 && p.minZoom < props.zoom ).map((punto,index) => {
      return (
          <Marker

            key={"M"+index+punto.id}

            icon={{
              fillColor: punto.color != undefined ? punto.color : "#333",
              path:punto.svgPath != undefined ? punto.svgPath : "M0,0h32v32H0V0z",
              scale: props.zoom != null ? ((props.zoom/2)*0.04) : 0,
              fillOpacity: punto.opacity != undefined ? punto.opacity : 1,
            }}

            position={{ lat: punto.lat, lng: punto.lng }}
            onClick={(e) => {props.onMarkerClick({"e":e})}}

            title={punto.title != undefined ? punto.title : ""}
          />
        )
      })}

    {(props.polylines.length > 0) && props.polylines.map((trazo,index) => {
      return (
        <Polyline

          key={"P"+index+trazo.id}
          options={{strokeColor: strokeColor != undefined ? '#'+trazo.color : '#ccc'}}
          path={trazo.georeferencias}

          onClick={(e) => {props.onPolylineClick({"e":e})}}

        />
      )
    })}

    {props.herramientaMapaActual == "drawingManagerPolyline" && <DrawingManager

      onPolygonComplete={(pol) => {
        props.onDrawingManagerPolylineComplete(pol)
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
    />}


  </GoogleMap>

)

export class Map extends Component {

  state = {
    "mapaStyle": styles.white,
    "mapTypeId":"roadmap",
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

  shouldComponentUpdate(nextProps, nextState) {

    if (
      nextProps.herramientaMapaActual != this.props.herramientaMapaActual ||
      nextProps.zoom != this.props.zoom
    ) {
      return true;
    }

    return false
  }

  render() {

    return (
      <div style={{ position: `absolute`, width:`100%`, zIndex:`20`, height:`100%` }}>

        <MyMapComponent

          opcionMenuActual={this.props.opcionMenuActual}
          herramientaMapaActual = {this.props.herramientaMapaActual}

          mapaStyle = {this.state.mapaStyle}
          mapTypeId = {this.state.mapTypeId}

          zoom={this.props.zoom}
          defaultCenter= {this.props.defaultCenter}

          onMapZoomChanged={(zoom) => { this.props.onMapZoom(zoom) }}
          onDrawingManagerPolylineComplete = {this.props.onDrawingManagerPolylineComplete}

          onMapClick={(e) => {this.props.onMapClick(e)}}
          onMarkerClick={this.props.onMarkerClick}
          onPolylineClick={this.props.onPolylineClick}

          markers={this.props.markers}
          polylines={this.props.polylines}

        />

        <div className="mapa-herramientas no-print">
            <TipoMapa
              handleMapaStyle={this.handleMapaStyle}
              handleMapaTypeId={this.handleMapaTypeId}

              handleHerramientaMapaActual={this.props.handleHerramientaMapaActual}
              herramientaMapaActual = {this.props.herramientaMapaActual}
            />
            <hr/>
        </div>

      </div>
    )
  }

}
