import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import logo from '../../img/tor.png';
import axios from 'axios';

export default class Footer extends Component{
    constructor(props) {
        super(props);
        this.state = {
            partners: null
        }
    }

    componentDidMount(){
        axios.get('/api/partner')
            .then(res => {
                this.setState({
                    partners: res.data
                })
            })
    }


    render() {
        const {partners} = this.state;
        return (
            <footer className="bg-blue-gradient text-green-inherit">
                <div className="container-fluid">
                    <div className="row align-items-stretch grey-separator">
                        <div className="col text-center mt-4 mb-4 d-flex flex-column align-items-center justify-content-around">
                            <div className="text-grey-inherit text-center">
                                <img src={logo} className="footer-logo" alt="logo"/>
                                <h1 className="h4">Race together</h1>
                                <div className="text-center">
                                    Rouler ensemble avant tout
                                </div>
                            </div>
                        </div>
                        <div className="col text-center text-grey-inherit mt-4 mb-4">
                            <h1><a name="partner">Partenaires</a></h1>
                                {partners && partners.length > 0 ?
                                    <ul className="nav flex-column">
                                        {partners.map(p => {
                                            return (
                                                <li className="nav-item">
                                                    <a href={'/partner/' + p.id} className="text-grey-inherit">{p.name}</a>
                                                </li>
                                            )
                                        })}
                                    </ul>
                                    :
                                    ''}
                        </div>
                        <div className="col text-center text-grey-inherit mt-4 mb-4 ">
                            <div>
                                <h1>Nous suivre</h1>
                                <ul className="nav flex-column">
                                    <li className="nav-item">
                                        <a className="nav-link text-grey-inherit" href="https://www.facebook.com/TeamOccitansRacingEsports/" target="_blank">
                                            <i className="fab fa-facebook mr-4"></i> Facebook Team occitans
                                        </a>
                                        <a className="nav-link text-grey-inherit" href="https://www.instagram.com/team_occitans_racing_esports/" target="_blank">
                                            <i className="fab fa-instagram mr-4"></i> Instagram Team occitans
                                        </a>
                                        <a className="nav-link text-grey-inherit" href="https://www.youtube.com/channel/UC8WGInNJNmLaz4clUqupTIA/featured" target="_blank">
                                            <i className="fab fa-youtube mr-4"></i> Youtube Team occitans
                                        </a>
                                        <a className="nav-link text-grey-inherit" href="https://www.twitch.tv/team_occitans_racing/" target="_blank">
                                            <i className="fab fa-twitch mr-4"></i> Twitch Team occitans
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div className="pt-3  text-center text-grey-inherit">
                        ©2020 Team occitans esport
                    </div>
                    <div className="pb-3 text-center text-grey-inherit">
                        powered by Back'n Dev
                    </div>
                </div>
            </footer>
        );
    }
}
ReactDOM.render(<Footer />, document.getElementById('footer'));