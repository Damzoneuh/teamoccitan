import React, {Component} from 'react';
import axios from 'axios';
import Loader from "../../../common/Loader";


export default class Result extends Component{
    constructor(props) {
        super(props);
        this.state = {
            results: null,
            isLoaded: false
        }
    }

    componentDidMount() {
        axios.get('/member/api/result')
            .then(res => {
                this.setState({
                    results: res.data,
                    isLoaded: true
                })
            })
    }

    render() {
        const {isLoaded, results} = this.state;

        if (!isLoaded){
            return(
                <Loader />
            )
        }

        else {
            return (
                <div className="row">
                    {results && results.length > 0 ?
                        results.map(r => {
                            return (
                                <div className="col-sm-6 col-md-4 col-lg-3">
                                    <div className="card rounded shadow-lg bg-blue-gradient" >
                                        <img src={'https://' + document.location.hostname + '/api/img/' + r.img.id} className="card-img-top" alt={r.img.name} />
                                        <div className="card-body text-grey-inherit">
                                            <h5 className="card-title">{r.name}</h5>
                                            <p className="card-text news-box">{r.description}</p>
                                            <p><small className="text-success">{r.author}</small> </p>
                                        </div>
                                    </div>
                                </div>
                            )
                        })
                        : ''}
                </div>
            )
        }
    }

}