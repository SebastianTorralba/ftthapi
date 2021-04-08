import React, {Component} from 'react';
import { compose, withProps } from "recompose"
import { withScriptjs, withGoogleMap, GoogleMap, Marker } from "react-google-maps"
const { MarkerClusterer } = require("react-google-maps/lib/components/addons/MarkerClusterer");

const google = window.google;

const MyMapComponent = compose(
  withProps({
    googleMapURL: "https://maps.googleapis.com/maps/api/js?key=AIzaSyD0ltBbgLWM4jf9tVmf_-f9lHzg0-cmw4Q",
    loadingElement: <div style={{ height: `100%` }} />,
    containerElement: <div style={{ height: `100%` }} />,
    mapElement: <div style={{ height: `100%` }} />,
  }),
  withGoogleMap
)((props) =>
  <GoogleMap
    defaultZoom={14}
    defaultCenter={{lat:-29.412771,lng:-66.855819}}
    defaultMapTypeId= {'satellite'}
  >
    <MarkerClusterer
      defaultZoomOnClick={true}
      minimumClusterSize={3}
      defaultMaxZoom={17}
      averageCenter
      styles={[{
        textColor: 'white',
        url: 'https://raw.githubusercontent.com/googlemaps/v3-utility-library/master/markerclustererplus/images/m1.png',
        height: 50,
        width: 50
      }]}
      enableRetinaIcons
      gridSize={60}
    >
      {props.markers.map((marker,index) => (
        <Marker
          key={marker.id}
          position={{ lat: parseFloat(marker.latitud), lng: parseFloat(marker.longitud) }}
          title={marker.title}
          defaultIcon={{
              path: google.maps.SymbolPath.CIRCLE,
              scale: 5,
              fillColor: "#fff",
              strokeColor: "#182983",
              fillOpacity: 1
          }}
        />

      ))}

    </MarkerClusterer>

  </GoogleMap>
)

export class Map extends Component {

  constructor(props) {
    super(props)
  }

  render() {
    return (
      <MyMapComponent markers={this.props.markers} />
    );
  }

}
