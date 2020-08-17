import React, { Component } from 'react';
import {connect} from 'react-redux';
import Header from "./components/Header"
import NavBar from "./components/NavBar"
import Footer from "./components/Footer"

import "./AboutUs.css"

class AboutUs extends Component { 


    constructor(props) {
        super(props);

    this.state={
            lg:this.props.lg[this.props.Header.language],
            render:this.props.render,
            // LinesStyle:{display:"flex",flexFlow:"wrap "+this.props.Header.flxdir}
        }

    }

componentWillMount(){

    //this.props.fetchcontacts();
    // this.props.fetchalldata('none','none');
    
}

componentDidUpdate(){
    if(this.state.render!=this.props.render){
        this.setState({
            lg:this.props.lg[this.props.Header.language],
            render:this.props.render,
        })
    }
}

    render() {
        return (

<div>

        {(this.props.main!=true)?<Header/>:null}
        {(this.props.main!=true)?<NavBar/>:null}

            <div  class="aboutcontainer">
            <h1   style={{textAlign:(this.props.main==true)?"center":this.props.Header.direction,margin:'10px'}}>{this.props.about.PageName}</h1>
           
            <h3   style={{textAlign:(this.props.main==true)?"center":this.props.Header.direction,whiteSpace:'pre',margin:'10px'}}>{this.props.about.Details}</h3>

            <br></br>
            </div>
            
        {(this.props.main!=true)?<Footer/>:null}
            </div>
);}

}

const mapStateToProps = state => ({
    
    about: state.submit.pages.about,
    branchs: state.submit.pages.contact.branchs,
    lg:state.submit.languages,
    Header:state.submit.Header.style,
    render:state.submit.Header.render,
}); 




 export default connect(mapStateToProps,{})(AboutUs);