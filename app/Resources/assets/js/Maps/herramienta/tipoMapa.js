import React, {Component} from 'react';
import { Button, Popover, PopoverHeader, PopoverBody, ButtonGroup } from 'reactstrap';
import {styles} from '../opciones.json'

export class TipoMapa extends Component {

  constructor(props) {
    super(props);

    this.state = {
      popoverOpen: false
    };
  }

  toggle = () => {
    this.setState({
      popoverOpen: !this.state.popoverOpen
    });
  }

  render() {
    return (
      <span>
        <Button className="mr-1" title="Cambiar modo mapa" color="light" id={'popover-herramienta-tipo-mapa'} onClick={this.toggle}>
          <i className="fa fa-map-o"></i>
        </Button>
        <Popover className="menuPopover" placement={'right'} isOpen={this.state.popoverOpen} target={'popover-herramienta-tipo-mapa'} toggle={this.toggle}>
          <PopoverHeader>Modo mapa</PopoverHeader>
          <PopoverBody>
            <ul className="list-group list-group-flush">
              <li key="mapa-style-white" name="mapa-style-white" onClick={() => {this.props.handleMapaStyle(styles.white); this.props.handleMapaTypeId("roadmap");}} className="list-group-item">Mapa claro</li>
              <li key="mapa-style-black" name="mapa-style-black" onClick={() => {this.props.handleMapaStyle(styles.black); this.props.handleMapaTypeId("roadmap");}} className="list-group-item">Mapa oscuro</li>
              <li key="mapa-style-comun" name="mapa-style-comun" onClick={() => {this.props.handleMapaStyle(styles.comun); this.props.handleMapaTypeId("roadmap");}} className="list-group-item">Mapa Común</li>
              <li key="mapa-style-satellite" name="mapa-type-satellite" onClick={() => {this.props.handleMapaStyle(styles.hybrid); this.props.handleMapaTypeId("satellite");}} className="list-group-item">Mapa satelital</li>
            </ul>
          </PopoverBody>
        </Popover>
      </span>
    );
  }
}
