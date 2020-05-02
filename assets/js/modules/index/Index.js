import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import pilot from '../../../img/pilot.jpeg';
import film from '../../../img/film.jpg';
import {ParallaxBanner, ParallaxProvider} from "react-scroll-parallax/cjs";
import lmp1 from '../../../img/lmp1.png';
import lmp2 from '../../../img/lmp2.png';

export default class Index extends Component{
    constructor(props) {
        super(props);
    }


    render() {
        return (
            <div>
                <div className="container-fluid">
                    <div className="row align-items-stretch">
                        <div className="col-sm-12 col-md-4">
                            <div className="text-grey-inherit mt-4 mb-4">
                                <div className="position-relative">
                                    <img src={pilot} alt="pilot" className="d-block w-100 h-250"/>
                                    <div className="position-absolute layer bg-black-inherit d-flex align-items-center justify-content-center">
                                        <a href="https://board.ipitting.com/team/teamoccitansracing/drivers/" className="text-grey-inherit scale border pt-2 pb-2 pl-3 pr-3 text-decoration-none" target="_blank">Nos pilotes</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-sm-12 col-md-4">
                            <div className="text-grey-inherit mt-4 mb-4">
                                <div className="position-relative">
                                    <img src={lmp2} alt="pilot" className="d-block w-100 h-250"/>
                                    <div className="position-absolute layer bg-black-inherit d-flex align-items-center justify-content-center">
                                        <a href="https://board.ipitting.com/team/teamoccitansracing/" className="text-grey-inherit scale border pt-2 pb-2 pl-3 pr-3 text-decoration-none" target="_blank">La team</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-sm-12 col-md-4">
                            <div className="text-grey-inherit mt-4 mb-4">
                                <div className="position-relative">
                                    <img src={film} alt="pilot" className="d-block w-100 h-250"/>
                                    <div className="position-absolute layer bg-black-inherit d-flex align-items-center justify-content-center">
                                        <a href="https://www.twitch.tv/team_occitans_racing/" className="text-grey-inherit scale border pt-2 pb-2 pl-3 pr-3 text-decoration-none" target="_blank">Le live</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <ParallaxProvider>
                    <ParallaxBanner layers={[
                        {
                            image: lmp1,
                            amount: -0.3,
                        }
                    ]}
                    style={{
                        height: '40vh',
                    }}
                    >
                        <div className="d-flex justify-content-around align-items-center h-100">
                            <a href="https://www.youtube.com/channel/UC8WGInNJNmLaz4clUqupTIA/featured" className="text-blue-inherit z-1500 p-4 scale text-decoration-none" title="chaine Youtube Team Occitans" target="_blank">
                                 <span style={{fontSize:'15rem', zIndex: 1500}}>
                                    <i className="fab fa-youtube text-grey-inherit"></i>
                                 </span>
                            </a>
                        </div>
                    </ParallaxBanner>
                </ParallaxProvider>
            </div>
        );
    }

}

ReactDOM.render(<Index />, document.getElementById('index'));