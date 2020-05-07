import React, {Component} from 'react';

export default class Loader extends Component{
    constructor(props) {
        super(props);
    }


    render() {
        return (
            <div className="text-center">
                <div className="spinner-border text-blue mt-4 mb-4" role="status" style={{width: '8rem', height: '8rem'}}>
                    <span className="sr-only"></span>
                </div>
            </div>
        );
    }

}