import React, { Component } from 'react';
import {connect} from 'react-redux';
// import {fetchcontacts,fetchtasks,fetchsupplies,fetchteams,fetchalldata} from '../actions/getactions';
import Header from "./components/Header"
import NavBar from "./components/NavBar"
import Footer from "./components/Footer"


// import Facebook from "./Social media Icons/facebook2.png";
// import Twitter from "./Social media Icons/twitter2.png";
// import Instagram from "./Social media Icons/instagram2.png";
// import YouTube from "./Social media Icons/youtube2.png";
import "./contact.css"

class Contact extends Component {

    constructor(props) {
        super(props);

    this.state={
            lg:this.props.lg[this.props.Header.language],
            render:this.props.render

            // LinesStyle:{display:"flex",flexFlow:"wrap "+this.props.Header.flxdir}
        }

    }

    static getDerivedStateFromProps(props, state){
   
        if(props.render>state.render)
       return {
        lg:props.lg[props.Header.language],
        render:props.render
    
        }
    
    }

    render() {
        return (

<div style={{padding:(this.props.main==true)?"10px":null,background:(this.props.main==true)?"#E8E8E8":null}}>

{(this.props.main!=true)?<Header/>:null}
{(this.props.main!=true)?<NavBar/>:null}
<br></br>
           <h1   style={{margin:"10px",textAlign:(this.props.main==true)?"center":this.props.Header.direction}}>{this.props.contact.PageName}</h1>


<div>
<p class="contacttag"  style={{flexDirection:this.props.Header.flxdir,textAlign:this.props.Header.direction}}><spane class="tag"> {this.state.lg.cnct.tl} </spane><spane>&nbsp;:&nbsp;</spane><spane>{this.props.contact.Telephone}</spane></p>
<p class="contacttag"  style={{flexDirection:this.props.Header.flxdir,textAlign:this.props.Header.direction}}><spane class="tag"> {this.state.lg.cnct.ml} </spane><spane>&nbsp;:&nbsp;</spane><spane>{this.props.contact.email}</spane></p>
<br></br>
<p class="contacttag"  style={{flexDirection:this.props.Header.flxdir,textAlign:this.props.Header.direction}}><spane class="tag"> {this.state.lg.cnct.brn} </spane></p>

{
    this.props.branchs.map( branch =>
<p class="contacttag"  style={{flexDirection:this.props.Header.flxdir,textAlign:this.props.Header.direction}}>
        
        <spane class="tag" >&nbsp; {branch.BranchName}&nbsp;</spane>

         <spane>&nbsp;{this.state.lg.cnct.brnAdd}&nbsp;</spane>
        <spane>:</spane>
         <spane> {branch.BranchLocation}</spane>
         <br></br>
          </p>
          
    )
}




</div>



{(this.props.main!=true)?<Footer/>:null}
            </div>
);}

}

const mapStateToProps = state => ({
    
    contact: state.submit.pages.contact,
    branchs: state.submit.pages.contact.branchs,
    lg:state.submit.languages,
    Header:state.submit.Header.style,
    render:state.submit.Header.render

    
});




 export default connect(mapStateToProps,{})(Contact);