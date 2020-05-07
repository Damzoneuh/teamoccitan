import React, {Component} from 'react';
import ReactDom from 'react-dom';
import axios from 'axios';
import Loader from "../../common/Loader";

export default class News extends Component{
    constructor(props) {
        super(props);
        this.state = {
            news: null,
            newsSelected: null,
            isLoaded: false
        }
    }

    componentDidMount(){
        axios.get('/api/news')
            .then(res => {
                this.setState({
                    news: res.data,
                    isLoaded: true
                })
            })
    }

    render() {
        const {isLoaded, news} = this.state;
        console.log(window.location.hostname)
        if (!isLoaded) {
            return (
                <Loader />
            )
        }
        else {
            return (
                <div className="container-fluid">
                    <div className="row align-items-stretch">
                        {news && news.length > 0 ?
                                news.map(n => {
                                    return (
                                        <div className="col-sm-12 col-md-6 col-lg-4 mt-4 mb-4">
                                            <div className="bg-blue-gradient rounded shadow-lg p-4 h-100">
                                                <div className="border-bottom mb-2">
                                                    <h2 className=" text-center text-grey-inherit">{n.title}</h2>
                                                </div>
                                                <div className="p-2 text-grey-inherit news-box">
                                                    {n.text}
                                                </div>
                                                <div className="mt-2 text-center">
                                                    <a title={n.title} href={'/news/' + n.id} className="btn btn-group btn-success">Voir plus</a>
                                                </div>
                                            </div>
                                        </div>
                                    )
                                })
                            :
                            <div className="mt-4 mb-4 col text-center">
                                <h1 className="text-center text-blue-inherit">Pas de nouvelle actus</h1>
                            </div>
                            }
                    </div>
                </div>
            );
        }
    }
}
ReactDom.render(<News />, document.getElementById('news'));