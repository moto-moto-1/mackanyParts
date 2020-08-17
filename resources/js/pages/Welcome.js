import React, { Component } from 'react';
import {connect} from 'react-redux';
// import {fetchcontacts,fetchtasks,fetchsupplies,fetchteams,fetchalldata} from '../actions/getactions';
import Header from "./components/Header"
import NavBar from "./components/NavBar"
import Products from "./components/Products"

import Services from "./components/Services"
import Contact from "./Contact"
import AboutUs from "./AboutUs"

import { Link} from 'react-router-dom'

import Footer from "./components/Footer"


class Welcome extends Component {

    constructor(props) {
        super(props);
this.state={
    lg:this.props.lg[this.props.header.style.language],

}

    }

componentWillMount(){

    //this.props.fetchcontacts();
    // this.props.fetchalldata('none','none');
    
}

    render() {
        return (

<div>
            <Header/>

            <NavBar/>
            <AboutUs main={true}/>
            <Contact main={true}/>
            {(this.props.products.exists)?<div><Products main_page={true}/><center><h2><Link to={"/products"}>  {this.state.lg.pr.mr}</Link></h2></center></div>:null}
            
            {(this.props.services.exists)?<div style={{padding:"10px",background:(this.props.products.exists)?"#E8E8E8":null}}><Services main_page={true}/><center><h2><Link to={"/services"}>  {this.state.lg.sv.mr}</Link></h2></center></div>:null}
            <Footer/>
            </div>
);}

}

const mapStateToProps = state => ({
    products: state.submit.pages.products,
    services: state.submit.pages.services,
    pages: state.submit.pages,
    requests: state.get.requests,
    header: state.submit.Header,
    lg:state.submit.languages,
    
});




 export default connect(mapStateToProps,{})(Welcome);