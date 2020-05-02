import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import Events from "./events/Events";

export default class Member extends Component{
    constructor(props) {
        super(props);
        this.state = {
            tab: 1
        };
        this.handleTab = this.handleTab.bind(this);
    }

    handleTab(tab){
        this.setState({
            tab: tab
        })
    }


    render() {
        const {tab} = this.state;
        return (
            <div className="container-fluid">
                <div className="row mt-4 mb-4">
                    <div className="col-12 mb-4 mt-2 bg-blue-gradient text-grey-inherit">
                        <div className="p-4">
                            <ul className="nav nav-tabs">
                                <li className="nav-item">
                                    <a
                                        className={tab === 1 ? 'nav-link text-green-inherit link' : 'nav-link text-grey-inherit link'}
                                        onClick={() => this.handleTab(1)}>Evènements</a>
                                </li>
                                <li className="nav-item">
                                    <a
                                        className={tab === 2 ? 'nav-link text-green-inherit link' : 'nav-link text-grey-inherit link'}
                                        onClick={() => this.handleTab(2)}>Résultats</a>
                                </li>
                                <li className="nav-item">
                                    <a
                                        className={tab === 3 ? 'nav-link text-green-inherit link' : 'nav-link text-grey-inherit link'}
                                        onClick={() => this.handleTab(3)}>Setup</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div className="col-12">
                        {tab === 1 ?
                            <div>
                                <h1 className="text-center text-blue mt-4 mb-4">
                                    Evènements
                                </h1>
                                <Events />
                            </div>
                            : ''}
                    </div>
                </div>
            </div>
        );
    }
}

ReactDOM.render(<Member />, document.getElementById('member'));