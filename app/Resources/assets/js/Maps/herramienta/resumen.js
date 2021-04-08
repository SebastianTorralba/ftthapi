import React, {Component} from 'react';

export class Resumen extends Component {

  state = {
    resumen: {}
  }

  constructor(props) {
    super(props);
  }

  componentWillReceiveProps(nextProps) {

    if (JSON.stringify(nextProps.markers) !== JSON.stringify(this.props.markers) && nextProps.markers.length > 0) {

      let resumen = {}

      nextProps.markers.map((m) => {

        if (m.elemento == null || m.elemento == undefined || m.elemento.resumen == undefined) {

          if (resumen["undefined"] == undefined) {

            resumen = {
              ...resumen,
              ...{
                "undefined": {
                  "descripcion": "undefined",
                  "cantidad": 1
                }
              }
            }

          } else {

            resumen["undefined"]["cantidad"] = resumen["undefined"]["cantidad"] + 1

          }

        } else {

          // para contabilizar la categoría
          if (resumen[m.elemento.resumen.categoria] == undefined) {

            resumen = {
              ...resumen,
              ...{
                [m.elemento.resumen.categoria]: {
                  "descripcion": m.elemento.resumen.categoria,
                  "cantidad": 1
                }
              }
            }

          } else {
            resumen[m.elemento.resumen.categoria]["cantidad"] = resumen[m.elemento.resumen.categoria]["cantidad"] + 1
          }

          // para contabilizar el tipo de elemento
          if (m.elemento.resumen.elementos != undefined && m.elemento.resumen.elementos != null) {

            m.elemento.resumen.elementos.map((e) => {

              if (resumen[m.elemento.resumen.categoria][e.id] == undefined) {

                resumen[m.elemento.resumen.categoria] ={
                  ...resumen[m.elemento.resumen.categoria],
                  ...{
                    [e.id]: {
                      "descripcion": e.descripcion,
                      "cantidad": e.cantidad,
                      "unidad": e.unidad
                    }
                  }
                }

              } else {
                resumen[m.elemento.resumen.categoria][e.id]["cantidad"] = resumen[m.elemento.resumen.categoria][e.id]["cantidad"] + e.cantidad
              }

            })

          }

        }

      })

      this.setState({
        resumen: resumen
      })

    }

  }

  _render2(categoria) {

    let elementoTotales = []

    Object.keys(categoria).map((key)=>{

      if (key != 'cantidad' && key != 'descripcion') {
        elementoTotales.push([categoria[key].descripcion, categoria[key].cantidad])
      }

    })

    return elementoTotales
  }

  _render() {

    return (
      <div style={{ backgroundColor:'#FFF', border:'solid 1px #333', position:`absolute`,bottom:`10px`,left:`10px`, padding: `5px`, maxWidth: `80%`, zIndex:`1000` }} >


          {Object.keys(this.state.resumen).map((key) => {

            return (
              <div style={{backgroundColor:'#ccc;'}}>
                <h6 key={'resumen-categoria-'+key} >{this.state.resumen[key].descripcion} ({this.state.resumen[key].cantidad})</h6>
                <div className="row" style={{marginBottom:'5px', color:'#555'}}>
                  <div className="col-12">
                    {this._render2(this.state.resumen[key]).map((elemento) => {
                      return <div key={"resumen-elemento"+elemento[0]} style={{float:'left', marginRight:'10px'}}><b>{elemento[0]}</b>({elemento[1]})</div>
                    })}
                  </div>
                </div>
              </div>
            )

          })}


      </div>
    );
  }


  render() {

    return this._render();
  }
}
