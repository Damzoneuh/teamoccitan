import React, {Component} from 'react';
import axios from 'axios';
import Loader from "../../../common/Loader";

export default class Setup extends Component{
    constructor(props) {
        super(props);
        this.state = {
            setups: null,
            isLoaded: false,
            buttonLoaded: true,
            cars: null,
            tracks: null,
            trackSelected: null,
            carSelected: null
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidMount() {
        axios.get('/api/car')
            .then(res => {
                this.setState({
                    cars: res.data
                })
                axios.get('/api/track')
                    .then(res => {
                        this.setState({
                            tracks: res.data,
                            isLoaded: true
                        })
                    })
            })
    }

    handleChange(e){
        this.setState({
            [e.target.name]: e.target.value
        })
    }

    handleSubmit(e){
        e.preventDefault();
        this.setState({
            buttonLoaded: false
        })
        axios.get('/pilot/api/setup/' + this.state.trackSelected + '/' + this.state.carSelected)
            .then(res => {
                this.setState({
                    setups: res.data,
                    buttonLoaded: true
                })
            })
    }

    render() {
        const {setups, isLoaded, cars, tracks, buttonLoaded} = this.state;
        if (!isLoaded){
            return (
                <Loader />
            )
        }
        else {
            return(
                <div className="row">
                    <div className="col-12">
                        <div className="p-lg-4 mt-4 mb-4 bg-blue-gradient text-grey-inherit">
                            <form onChange={this.handleChange} onSubmit={this.handleSubmit}>
                                <h2 className="text-center mt-2 mb-2" >Setups</h2>
                                <div className="form-row">
                                    <div className="col">
                                        <label htmlFor="carSelected">Voiture</label>
                                        <select name="carSelected" className="form-control" required={true}>
                                            <option></option>
                                            {cars && cars.length > 0 ?
                                                cars.map(c => {
                                                    return (
                                                        <option value={c.id} >{c.name}</option>
                                                    )
                                                }) : ''
                                            }
                                        </select>
                                    </div>
                                    <div className="col">
                                        <label htmlFor="trackSelected">Circuit</label>
                                        <select name="trackSelected" className="form-control">
                                            <option></option>
                                            {tracks && tracks.length > 0 ?
                                                tracks.map(t => {
                                                    return(
                                                        <option value={t.id}>{t.name}</option>
                                                    )
                                                })
                                                : ''}
                                        </select>
                                    </div>
                                    <div className="col">
                                        <div className="d-flex justify-content-center align-items-end h-100">
                                            <button className="btn btn-group btn-success">{buttonLoaded ? 'Rechercher' :
                                                <div className="text-center">
                                                    <div className="spinner-border text-grey-inherit" role="status">
                                                        <span className="sr-only"></span>
                                                    </div>
                                                </div>
                                            }</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div className="col-12">
                        {setups && setups.length > 0 ?
                            <table className="table mt-4 mb-4 text-blue">
                                <tbody>
                                {setups.map(s => {
                                    return (
                                        <tr>
                                            <td className="text-center">
                                                {s.name}
                                            </td>
                                            <td>
                                                <a href={'/pilot/setup/download/' + s.id} >
                                                    <i className="fas fa-file-download fa-2x text-success "></i>
                                                </a>
                                            </td>
                                        </tr>
                                    )
                                })}
                                </tbody>
                            </table>
                            : ''}
                    </div>
                </div>
            )
        }
    }
}