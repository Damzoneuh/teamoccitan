import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import Events from "./events/Events";
import Setup from "./setup/Setup";
import Result from "./reslut/Result";
import Skin from "./skin/Skin";

const el = document.getElementById('member');

export default class Member extends Component{
    constructor(props) {
        super(props);
        this.state = {
            tab: 2
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
                            {el.dataset.pilot === '1' ?
                                <ul className="nav nav-tabs">
                                    <li className="nav-item">
                                        <a
                                            className={tab === 2 ? 'nav-link text-green-inherit link' : 'nav-link text-grey-inherit link'}
                                            onClick={() => this.handleTab(2)}>Résultats</a>
                                    </li>
                                    <li className="nav-item">
                                        <a
                                            className={tab === 1 ? 'nav-link text-green-inherit link' : 'nav-link text-grey-inherit link'}
                                            onClick={() => this.handleTab(1)}>Evènements</a>
                                    </li>
                                    <li className="nav-item">
                                        <a
                                            className={tab === 3 ? 'nav-link text-green-inherit link' : 'nav-link text-grey-inherit link'}
                                            onClick={() => this.handleTab(3)}>Setup</a>
                                    </li>
                                    <li className="nav-item">
                                        <a
                                            className={tab === 4 ? 'nav-link text-green-inherit link' : 'nav-link text-grey-inherit link'}
                                            onClick={() => this.handleTab(4)}>Skin</a>
                                    </li>
                                </ul>
                                :
                                <ul className="nav nav-tabs">
                                    <li className="nav-item">
                                        <a
                                            className={tab === 2 ? 'nav-link text-green-inherit link' : 'nav-link text-grey-inherit link'}
                                            onClick={() => this.handleTab(2)}>Résultats</a>
                                    </li>
                                </ul>
                            }

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
                        {tab === 2 ? <Result /> : ''}
                        {tab === 3 ? <Setup /> : ''}
                        {tab === 4 ? <Skin /> : ''}
                    </div>
                </div>
            </div>
        );
    }
}

ReactDOM.render(<Member />, document.getElementById('member'));