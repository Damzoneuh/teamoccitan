import React, {Component} from 'react';
import axios from 'axios';
import Loader from "../../../common/Loader";

export default class Skin extends Component{
    constructor(props) {
        super(props);
        this.state = {
            skins: null,
            isLoaded: null
        }
    }

    componentDidMount() {
        axios.get('/pilot/api/skin')
            .then(res => {
                this.setState({
                    skins: res.data,
                    isLoaded: true
                })
            })
    }

    render() {
        const {isLoaded, skins} = this.state;
        if (!isLoaded){
            return (
                <Loader />
            )
        }
       else {
           return (
               <div className="container-fluid">
                   <div className="row">
                       <div className="col-12">
                           <h1 className="text-center text-blue">Skins</h1>
                       </div>
                       {skins && skins.length > 0 ?
                           <div className="col-12">
                               <table className="table mt-4 mb-4 text-blue">
                                   <tbody>
                                   {skins.map(s => {
                                       return (
                                           <tr>
                                               <td>{s.name}</td>
                                               <td>
                                                   <a href={'/pilot/download/skin/' + s.id} >
                                                       <i className="fas fa-file-download fa-2x text-success "></i>
                                                   </a>
                                               </td>
                                           </tr>
                                       )
                                   })}
                                   </tbody>
                               </table>
                           </div>
                           : ''}
                   </div>
               </div>
           )
        }
    }

}