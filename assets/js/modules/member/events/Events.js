import React, {Component} from 'react';
import axios from 'axios';
import Loader from "../../../common/Loader";
const el = document.getElementById('member');

export default class Events extends Component{
    constructor(props) {
        super(props);
        this.state = {
            events: null,
            isLoaded: false
        }
    }

    componentDidMount(){
        axios.get('/api/user/event')
            .then(res => {
                this.setState({
                    events: res.data,
                    isLoaded: true
                })
            })
    }

    render() {
        const {isLoaded, events} = this.state;
        if (!isLoaded){
            return (
                <Loader />
            )
        }
        else{
            return (
                <table className="table-striped table-responsive-sm table text-black-50">
                    <thead>
                        <tr className="text-center">
                            <th scope="col">Event</th>
                            <th scope="col">Date</th>
                            <th scope="col">Inscription</th>
                        </tr>
                    </thead>
                    {events && events.length > 0 ? events.map(e => {
                        let i = 0;
                        return (
                            <tr className="text-center">
                                <th scope="col">{e.name}</th>
                                <th scope="col">{e.date.slice(0, 4)}/{e.date.slice(5, 7)}/{e.date.slice(8, 10)} à {e.date.slice(11, 13)} h {e.date.slice(14, 16)}</th>
                                {e.pilotEngage && e.pilotEngage.length > 0 ? e.pilotEngage.map(u => {
                                    if (u.id === parseInt(el.dataset.user)){
                                        i ++;
                                    }
                                }) : ''}
                                {i > 0 ? <th><a className="text-green" href={'/pilot/event/' + e.id}>Voir</a></th>
                                    :
                                    <th scope="col"><a href={'/pilot/event/pilot/' + el.dataset.user + '/' + e.id} className="text-blue">S'inscrire</a></th>
                                }
                            </tr>
                        )
                    }) : ''}
                </table>
            );
        }
    }
}