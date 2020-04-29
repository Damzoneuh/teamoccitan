import React, {Component} from 'react';
import { Player } from 'video-react';


export default class Video extends Component{
    constructor(props) {
        super(props);
        this.state = {
            browser: null
        }
    }

    componentDidMount(){
        if (typeof window.InstallTrigger !== 'undefined'){
            this.setState({
                browser: '.ogv'
            })
        }
        else {
            this.setState({
                browser: '.mp4'
            })
        }
    }


    render() {
        const {browser} = this.state;
        if (!browser){
            return <div></div>
        }
        else {
            return (
                <Player
                    autoHide={true}
                    loop={true}
                    autoPlay={true}
                    muted={true}
                    src={'https://teamoccitan.timbahia.fr/file/porshe_intro' + browser } />
            );
        }
    }


}