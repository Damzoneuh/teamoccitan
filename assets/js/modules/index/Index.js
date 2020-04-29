import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import pilot from '../../../img/pilot.jpeg';

export default class Index extends Component{
    constructor(props) {
        super(props);
    }


    render() {
        return (
            <div className="container-fluid">
                <div className="row align-items-stretch">
                    <div className="col-sm-12 col-md-4">
                        <div className="text-grey-inherit mt-4 mb-4">
                            <div className="position-relative">
                                <img src={pilot} alt="pilot" className="d-block w-100"/>
                                <div className="position-absolute layer bg-black-inherit d-flex align-items-center justify-content-center">
                                    <a href="/pilot" className="text-grey-inherit scale border pt-2 pb-2 pl-3 pr-3 text-decoration-none">Nos pilotes</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

}

ReactDOM.render(<Index />, document.getElementById('index'));