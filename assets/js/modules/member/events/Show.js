import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import Loader from "../../../common/Loader";
import RelayHandler from "./RelayHandler";
const el = document.getElementById('event');

export default class Show extends Component{
    constructor(props) {
        super(props);
        this.state = {
            event: null,
            teams: null,
            isLoaded: false
        }
    }

    componentDidMount() {
        axios.get('/api/user/event/' + el.dataset.event)
            .then(res => {
                this.setState({
                    event: res.data,
                })
                axios.get('/pilot/api/team')
                    .then(res => {
                        this.setState({
                            isLoaded: true,
                            teams: res.data
                        })
                    })
            })
    }

    render() {
        const {isLoaded, event, teams} = this.state;
        if (!isLoaded){
            return (
                <Loader />
            )
        }
        else {
            return (
                <div className="container-fluid">
                    <div className="row mt-4 mb-4 text-grey-inherit">
                        <div className="col-12 mt-4 mb-4">
                            <h1 className="text-center text-blue">{event.name}</h1>
                        </div>
                        <div className="col-md-6 col-sm-12 bg-blue-gradient">
                            <div className="d-flex flex-column justify-content-around align-items-center mb-4 mt-4">
                                <h2 className="text-center mb-4">{event.track[0].name}</h2>
                                <img src={'https://' + document.location.hostname + '/api/img/' + event.track[0].img.id} className="img-fluid w-50" />
                            </div>
                        </div>
                        <div className="col-md-6 col-sm-12 bg-blue-gradient">
                            <div className="d-flex flex-column justify-content-around align-items-center mb-4 mt-4">
                                <div className="row justify-content-around">
                                    {event.car && event.car.length > 0 ?
                                        event.car.map(c => {
                                            return (
                                                <div className="col">
                                                    <div className="position-relative w-auto">
                                                        <img src={'https://' + document.location.hostname + '/api/img/' + c.img.id} className="w-100"/>
                                                        <div className="position-absolute layer bg-black-inherit">
                                                            <div className="d-flex justify-content-center align-items-center h-100">
                                                                {c.name}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            )
                                        })
                                        : ''}
                                </div>
                            </div>
                        </div>
                        <div className="col-12">
                            <RelayHandler teams={teams} pilots={event.pilotEngage} relays={event.relay} event={event}/>
                        </div>
                    </div>
                </div>
            )
        }
    }
}
ReactDOM.render(<Show />, document.getElementById('event'))