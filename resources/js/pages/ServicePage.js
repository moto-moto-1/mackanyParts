import React, { Component } from 'react';
import {connect} from 'react-redux';
// import {fetchcontacts,fetchtasks,fetchsupplies,fetchteams,fetchalldata} from '../actions/getactions';
import Header from "./components/Header"
import NavBar from "./components/NavBar"
import Services from "./components/Services"
import Footer from "./components/Footer"


class SericePage extends Component {

    
    


componentWillMount(){

// console.log(this.props.match.params.subpageurl)

// console.log("these are parameters")
    //this.props.fetchcontacts();
    // this.props.fetchalldata('none','none');
    
}

    render() {
        return (

<div>
            <Header/>
            <NavBar/>
            <Services subpage={this.props.subpage} subpageurl={this.props.match.params.subpageurl}/>
            <Footer/>
            </div>
);}

}

const mapStateToProps = state => ({
    services: state.submit.pages.services,
    pages: state.get.pages,
    requests: state.get.requests,
    
});




 export default connect(mapStateToProps,{})(SericePage);