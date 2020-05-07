import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import Loader from "../../common/Loader";

export default class Gallery extends Component{
    constructor(props) {
        super(props);
        this.state = {
            galleries: null,
            isLoaded: false
        }
    }

    componentDidMount() {
        axios.get('/api/gallery')
            .then(res => {
                this.setState({
                    galleries: res.data,
                    isLoaded: true
                })
            })
    }

    render() {
        const {isLoaded, galleries} = this.state;
        if (!isLoaded){
            return (
                <Loader />
            )
        }
        return (
            <div className="container-fluid">
                <div className="row mt-4 mb-4">
                    {galleries && galleries.length > 0 ?
                     galleries.map(g => {
                         return (
                             <div className="col">
                                 <img className="img-fluid" src={'https://' + document.location.hostname + '/api/img/' + g.img.id} alt={g.name}/>
                             </div>
                         )
                     })
                    : ''}
                </div>
            </div>
        )
    }
}

ReactDOM.render(<Gallery />, document.getElementById('gallery'));