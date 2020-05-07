import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import {ParallaxBanner, ParallaxProvider} from "react-scroll-parallax/cjs";
import Video from "./Video";

export default class Banner extends Component{
    constructor(props) {
        super(props);

    }


    render() {
        return (
            <div>
                <ParallaxProvider>
                    <ParallaxBanner
                        layers={[
                            {
                                children: <Video />,
                                amount: 0.3,
                                props:{className: 'banner'}
                            }
                        ]}
                    >
                    </ParallaxBanner>
                </ParallaxProvider>
            </div>
        );
    }
}

ReactDOM.render(<Banner />, document.getElementById('banner'))