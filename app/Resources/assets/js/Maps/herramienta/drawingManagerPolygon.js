import 'babel-polyfill';
import React, {Component} from 'react';


export class DrawingManagerPolygon extends Component {

  drawingManager=null

  constructor(props) {
    super(props);
  }

  componentWillMount()
  {
    this.drawingManager = new google.maps.drawing.DrawingManager({
      drawingMode: google.maps.drawing.OverlayType.POLYGON,
      drawingControl: true,
      drawingControlOptions: {
        position: google.maps.ControlPosition.TOP_CENTER,
        drawingModes: ['polygon']
      },

      polygonOptions: {
        strokeWeight: 1,
        editable: false,
        fillColor: `#018BFE`,
        fillOpacity:.5
      }
    });

    this.drawingManager.setMap(this.props.map);

    google.maps.event.addListener(this.drawingManager, 'polygoncomplete', (polygon) => {
      this.props.onDrawingManagerPolygonComplete(polygon)
    });

  }

  render() {
    return null;
  }

  componentWillUnmount(){
    if(this.drawingManager !=null) {
      this.drawingManager.setMap(null);
    }
  }
}
