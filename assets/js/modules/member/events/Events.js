import React, {Component} from 'react';
import axios from 'axios';
import Loader from "../../../common/Loader";

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
                        return (
                            <tr className="text-center">
                                <th scope="col">{e.name}</th>
                                <th scope="col">{e.date.slice(0, 4)}/{e.date.slice(5, 7)}/{e.date.slice(8, 10)} à {e.date.slice(11, 13)} h {e.date.slice(14, 16)}</th>
                                <th scope="col">S'inscrire</th>
                            </tr>
                        )
                    }) : ''}
                </table>
            );
        }
    }
}