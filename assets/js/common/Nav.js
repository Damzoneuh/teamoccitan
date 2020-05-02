import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import logo from '../../img/tor.png';

const el = document.getElementById('nav');

export default class Nav extends Component{
    constructor(props) {
        super(props);
        this.state = {
            scroll: 0
        };
        this.detectScroll = this.detectScroll.bind(this);
    }

    componentDidMount(){
        window.addEventListener('scroll', () => this.detectScroll())
    }

    detectScroll(){
        this.setState({
            scroll: window.scrollY
        })
    }

    render() {
        const {scroll} = this.state;
        return (
            <div className="bg-blue-gradient">
                <div className="text-center">
                    <img src={logo} alt="logo" className="nav-logo mt-4"/>
                    <div className="text-center ">
                        <h1 className="text-grey-inherit h-full mb-4 pb-2">Race Together</h1>
                    </div>
                </div>
                <nav className={scroll > 0 ? 'navbar navbar-expand-lg navbar-light fixed-top bg-blue-gradient grey-separator' : "navbar navbar-expand-lg navbar-light "}>
                    <button className="navbar-toggler bg-grey-inherit" type="button" data-toggle="collapse"
                            data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false"
                            aria-label="Toggle navigation">
                        <span className="navbar-toggler-icon"></span>
                    </button>
                    <div className="collapse navbar-collapse" id="navbarTogglerDemo01">
                        <ul className="navbar-nav mt-2 mt-lg-0 align-items-center justify-content-around w-100">
                            <li className="nav-item m-auto">
                                <a className="nav-link text-grey-inherit" href="/">Accueil </a>
                            </li>
                            <li className="nav-item m-auto">
                                <a className="nav-link text-grey-inherit" href="/news">Actus</a>
                            </li>
                            <li className="nav-item m-auto">
                                <a className="nav-link text-grey-inherit" href="#partner">Nos partenaires</a>
                            </li>
                            <li className="nav-item m-auto">
                                <a className="nav-link text-grey-inherit" href="/live">Live</a>
                            </li>
                            <li className="nav-item m-auto">
                                <a className="nav-link text-grey-inherit" href="#">Galerie</a>
                            </li>
                            {el.dataset.connected === '1' ?
                                <li className="nav-item dropdown">
                                    <a className="nav-link dropdown-toggle text-grey-inherit" href="#" id="navbarDropdown" role="button"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Espace Membre
                                    </a>
                                    <div className="dropdown-menu bg-blue-gradient" aria-labelledby="navbarDropdown">
                                        <a className="dropdown-item text-grey-inherit" href="/member">Mon espace</a>
                                        <a className="dropdown-item text-grey-inherit" href="/logout">Déconnexion</a>
                                    </div>
                                </li>
                                :
                                <li className="nav-item m-auto">
                                    <a className="nav-link text-grey-inherit" href="/login">Connexion</a>
                                </li>
                            }
                        </ul>
                    </div>
                </nav>
            </div>
        );
    }
}

ReactDOM.render(<Nav />, document.getElementById('nav'));