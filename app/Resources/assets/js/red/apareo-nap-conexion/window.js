import React, {Component} from 'react';
import {Menu} from './window/menu';
import {Body} from './window/body';

export class Window extends Component {

  constructor(props) {
    super(props)
  }

  render() {

      return (
        <div className="window no-print">
          <Menu {...this.props} />
          <Body {...this.props} />
        </div>
      )

  }
}
