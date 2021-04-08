import React, {Component} from 'react';
import { styles } from './opciones.json'
import {TipoMapa} from './herramienta/tipoMapa'
import {Conexiones} from './herramienta/conexiones'
import {Resumen} from './herramienta/resumen'
import {DrawingManagerPolygon} from './herramienta/drawingManagerPolygon';

class MapComponent extends Component {

  markers=[]
  markerCluster=[]
  markerGroup=[]

  polylines=[]
  map=null
  updateRender=1
  state = {
    load:0
  }

  constructor(props) {
    super(props)
  }

  onSriptLoad = () => {
    this.map = new window.google.maps.Map(document.getElementById(this.props.id), this.props.options)
    this.props.onMapLoad(this.map)

    this.map.addListener('zoom_changed', () => {
      this.props.onMapZoom(this.map.getZoom());
    });

    this.map.addListener('click', (e) => {
      this.props.onMapClick(e);
    });

  }

  componentWillReceiveProps(nextProps) {

    if(nextProps.markers!=this.props.markers) {
      this.setMarkers(nextProps.markers)
    }

    if(nextProps.polylines!=this.props.polylines) {
      this.setPolylines(nextProps.polylines)
    }

    if(nextProps.options.mapTypeId!=this.props.options.mapTypeId) {

      this.map.setOptions({styles: [{
        "featureType" : "road",
        "stylers" : [{
          "visibility" : "on"
        }]
      }]})

      this.map.setMapTypeId(nextProps.options.mapTypeId)
    }

    if(nextProps.options.styles!=this.props.options.styles) {
      this.map.setOptions({styles: nextProps.options.styles})
    }

    if (nextProps.options.center!=this.props.options.center) {
      this.map.setCenter(nextProps.options.center)
      this.map.setZoom(nextProps.options.zoom)
    }

  }

  addMarker(m) {

    let mar = new window.google.maps.Marker({
      id: m.id,
      position: {lat:m.lat,lng:m.lng},
      map:this.map,
      title:m.title,
      icon:  m.image != undefined ? 'http://'+(this.props.host+this.props.baseUrl).toString().replace(/app_dev.php/g,"") + m.image : "",
    })

    mar.addListener('click', (e) => {
      this.props.onMarkerClick({marker:m,e:e})
    });

  }

  setMarkers(markers) {

    let url = 'http://'+(this.props.host+this.props.baseUrl).toString().replace(/app_dev.php/g,"")

    this.markers.map((el) => {
      el.setMap(null)
    })
    this.markers=[]

    this.markerCluster.map((el) => {
      el.clearMarkers()
    })

    this.markerCluster=[]
    this.markerGroup=[]

    // armo los grupos para los markers
    markers.filter(m => m.groupBy!=undefined && m.groupBy!=null).map((m) => {
      if (this.markerGroup.indexOf(m.groupBy)==-1) {
        this.markerGroup.push(m.groupBy)
      }
    })

    this.markerGroup.map((groupId) => {

      let mg = markers.filter(m => m.groupBy==groupId).map((m) => {

        let mar = new window.google.maps.Marker({
          id:m.id,
          position:{lat:m.lat,lng:m.lng},
          title:m.title,
          icon:m.image != undefined ? url + m.image : "",
        })

        mar.addListener('click', (e) => {
          this.props.onMarkerClick({marker:m,e:e})
        });

        return mar;
      })

      this.markerCluster.push(new MarkerClusterer(this.map, mg,{maxZoom:15, imagePath: null}));
    })

    // defino los markers
    this.markers = markers.map((m) => {

      let mar = new window.google.maps.Marker({
        id:m.id,
        position:{lat:m.lat,lng:m.lng},
        map:m.groupBy!=undefined && m.groupBy!=null ? null : this.map,
        title:m.title,
        icon:m.image != undefined ? url + m.image : "",
      })

      mar.addListener('click', (e) => {
        this.props.onMarkerClick({marker:m,e:e})
      });

      return mar;
    })

  }

  setPolylines(polylines) {

    this.polylines.map((el) => {
      el.setMap(null)
    })

    this.polylines=[]

    this.polylines = polylines.map((p) => {

      let pol = new window.google.maps.Polyline({
        id: p.id,
        path: p.path,
        title:p.title,
        strokeColor: p.color,
        strokeWeight:2
      })

      pol.setMap(this.map)

      pol.addListener('click', (e) => {
        this.props.onPoliylineClick({polyline:p,e:e})
      });

      return pol;
    })

  }

  componenWilltUpdate() {
    return false
  }

  componentDidMount() {
    this.onSriptLoad()
    this.setState({
      load:1
    })
    /*if (!window.google) {
      var s = document.createElement('script')
      s.type = 'text/javascript';
      s
    } else {

    }*/
  }

  render(){
    return (
      <div
        id={this.props.id}
        style={{ position:`absolute`,top:`0`,left:`0`, height: `100%`, width: `100%`, zIndex:`100` }}
      >

        {this.state.load==1 && this.props.herramientaMapaActual.indexOf("drawingManagerPolygon") > -1 &&
          <DrawingManagerPolygon
            map={this.map}
            onDrawingManagerPolygonComplete = {this.props.onDrawingManagerPolygonComplete}
          />}

      </div>
    )
  }
}

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

  render(){

    return (
      <div>

        <MapComponent

           {...this.props}

           id="obra-map"
           onMapLoad={map =>{}}

           options={{
              center:this.props.defaultCenter,
              zoom:this.props.mapZoom,
              zoomControl: true,
              mapTypeControl: false,
              scaleControl: false,
              streetViewControl: true,
              streetViewControlOptions: {
                position: google.maps.ControlPosition.TOP_LEFT
              },
              rotateControl: true,
              fullscreenControl: false,
              mapTypeId: this.state.mapTypeId,
              styles: this.state.mapaStyle
          }}
        />

        <div className="mapa-herramientas no-print">

          <TipoMapa

            {...this.props}

            handleMapaStyle={this.handleMapaStyle}
            handleMapaTypeId={this.handleMapaTypeId}
          />

          <Conexiones
            {...this.props}
          />

        </div>

        <Resumen
          {...this.props}
        />

    </div>
    )
  }
}
