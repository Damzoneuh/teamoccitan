import React, {Component} from 'react';
import axios from 'axios';

export default class RelayHandler extends Component{
    constructor(props) {
        super(props);
        this.state = {
            pilots: this.props.pilots,
            relays: this.props.relays,
            teams: this.props.teams,
            event: this.props.event,
            eventSorted: null,
            selectedPilot: null,
            selectedTeam: null,
            selectedHour: null,
            selectedCar: null
        };
        this.sortDoubleO = this.sortDoubleO.bind(this);
        this.getEventSorted = this.getEventSorted.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
    }

    componentDidMount() {
        this.getEventSorted()
    }

    getEventSorted(){
        let event = this.props.event;
        let hour = parseInt(event.date.slice(11, 13));
        let minutes = parseInt(event.date.slice(14,16));
        let relays = this.props.relays;
        let team = this.props.teams;
        let table = [];

        for (let i = 0; event.duration > i; i++){
            let row = {
                hour: this.sortDoubleO(hour + i) + ' ' + this.sortDoubleMinutes(minutes),
                rel: []
            };
            for(let i = 0; i < team.length; i++){
                row.rel.push(null);
            }
            team.map((t, key) => {
                if (relays && relays.length > 0){
                    relays.map(r => {
                        if (r.team.id === t.id && r.timeOffset === i){
                            row.rel[key] = {
                                pilot: r.pilot,
                                car: r.car,
                                team: r.team.id,
                                id: r.id
                            };
                        }
                    });
                }
            })
            table.push(row);
            this.setState({
                eventSorted: table
            })
        }
    }

    sortDoubleO(time){
        if (time === 0){
            return '00';
        }
        if(time < 10){
            return '0' + time
        }
        if (time > 24){
            time = time - 24;
            if (time < 10){
                return '0' + time;
            }
            return time;
        }
        return time;
    }

    sortDoubleMinutes(min){
        if (parseInt(min) < 10){
            return '0' + min;
        }
        return min;
    }

    handleChange(e){
        this.setState({
            [e.target.name]: e.target.value
        })
    }

    handleSubmit(e){
        e.preventDefault();
        let payLoad = {
            offset: this.state.selectedHour,
            pilot: this.state.selectedPilot,
            team: this.state.selectedTeam,
            car: this.state.selectedCar,
            event: this.state.event.id
        };
        axios.post('/pilot/api/relay/create', payLoad)
            .then(res => {
                this.setState({
                    selectedHour: null,
                    selectedPilot: null,
                    selectedTeam: null,
                    selectedCar: null,
                    isLoaded: true
                });
                this.getEventSorted();
                window.location.href = '/pilot/event/' + this.state.event.id
            })
            .catch(e => {
                this.getEventSorted();
                this.setState({
                    isLoaded: true
                });
                window.location.href = '/pilot/event/' + this.state.event.id
            })
    }

    handleDelete(id){
        axios.delete('/pilot/api/relay/delete/' + id)
            .then(res => {
                window.location.href = '/pilot/event/' + this.state.event.id;
            })
    }

    render() {
        const {pilots, teams, event, eventSorted} = this.state;
        return (
            <div className="row">
                <div className="col-12 mb-4">
                    <h1 className="text-center mt-4 mb-4 text-blue">
                        Pilotes inscrits
                    </h1>
                    {pilots && pilots.length > 0 ? pilots.map(p => {
                        return (
                            <div className="text-center text-green">
                                <small>{p.name} {p.lastname}</small>
                            </div>
                        )
                    }) : ''}
                </div>
                <div className="col-12 mb-4">
                    <form className="bg-blue-gradient text-grey-inherit p-4" onChange={this.handleChange} onSubmit={this.handleSubmit}>
                        <h3 className="text-center">Ajouter un relais</h3>
                        <div className="form-row">
                            <div className="col">
                                <label htmlFor="selectedPilot">Pilote</label>
                                <select name="selectedPilot" id="selectedPilot" className="form-control" required={true}>
                                    <option></option>
                                    {pilots.map(p => {
                                        return(
                                            <option value={p.id} >{p.name} {p.lastname}</option>
                                        )
                                    })}
                                </select>
                            </div>
                            <div className="col">
                                <label htmlFor="selectedTeam">Team</label>
                                <select name="selectedTeam" id="selectedTeam" className="form-control" required={true}>
                                    <option></option>
                                    {teams.map(t => {
                                        return (
                                            <option value={t.id}>{t.name}</option>
                                        )
                                    })}
                                </select>
                            </div>
                            <div className="col">
                                <label htmlFor="selectedHour">Heure début de relais (1h)</label>
                                <select className="form-control" id="selectedHour" name="selectedHour" required={true}>
                                    <option></option>
                                    {eventSorted && eventSorted.length > 0 ? eventSorted.map(es => {
                                        return (
                                            <option value={parseInt(es.hour.slice(0,2)) - parseInt(event.date.slice(11, 13))}>{es.hour}</option>
                                        )
                                    }) : ''}
                                </select>
                            </div>
                            <div className="col">
                                <label htmlFor="selectedCar">Voiture</label>
                                <select className="form-control" id="selectedCar" name="selectedCar" required={true}>
                                    <option></option>
                                    {event.car.map(c => {
                                        return (
                                            <option value={c.id}>{c.name}</option>
                                        )
                                    })}
                                </select>
                            </div>
                        </div>
                        <div className="text-center mt-4 mb-4">
                            <button className="btn btn-group btn-success">Valider</button>
                        </div>
                    </form>
                </div>
                <table className="table">
                    <thead>
                        <tr>
                            <td scope="col">Heures</td>
                            {teams && teams.length > 0 ?
                                teams.map(t => {
                                    return (
                                        <td scope="col" className="text-center" key={t.id}>{t.name}</td>
                                    )
                                })
                                : ''}
                        </tr>
                    </thead>
                    <tbody>
                    {eventSorted && eventSorted.length > 0 ? eventSorted.map(relay => {
                        return (
                            <tr>
                                <td scope="row">{relay.hour}</td>
                                {relay.rel.map(r => {
                                    if (!r){
                                        return(
                                            <td className="bg-danger text-blue text-center">Personne sur ce relais</td>
                                        )
                                    }
                                    else {
                                        return (
                                            <td className="bg-blue-gradient text-grey-inherit">
                                                <div className="d-flex align-items-center justify-content-around">
                                                    <div >
                                                        <div className="text-center">
                                                            {r.pilot.name}
                                                        </div>
                                                        <div className="text-center">
                                                            {r.pilot.lastname}
                                                        </div>
                                                        <div className="text-center">
                                                            {r.car.name}
                                                        </div>
                                                    </div>
                                                    <div className="text-center">
                                                        <i className="fas fa-trash text-danger link" onClick={() => this.handleDelete(r.id)}></i>
                                                    </div>
                                                </div>
                                            </td>
                                        )
                                    }
                                })}
                            </tr>
                        )
                    }) : ''}
                    </tbody>
                </table>
            </div>
        )
    }
}