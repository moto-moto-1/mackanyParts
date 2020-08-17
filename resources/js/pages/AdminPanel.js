import React, { Component } from 'react';
import {connect} from 'react-redux';

import NavBar from "./components/NavBar"

import PageListItem from "./components/PageListItem"
import ControlInput from "./components/ControlInput"

import {changecontrolpage,getInitialData} from "../actions/submitaction"

import "./AdminPanel.css"


class AdminPanel extends Component {

    constructor(props) {
        super(props);
        if(this.props.UserData.signedin)axios.defaults.headers.common['Authorization'] = 'Bearer '+this.props.UserData.jwtToken;

        this.props.getInitialData();
        this.state={
            lg:this.props.lg[this.props.Header.language],
            render:this.props.header.render
        }

        //this.state = {activeMainPage:"products"}
}

// static getDerivedStateFromProps(props, state){

//     return {
//         lg:props.lg[props.Header.language]
//     } 
//   }

componentDidUpdate(prevProps, prevState, snapshot){
    if (this.props.header.render != prevProps.header.render||prevProps.header.render!=this.state.render
        ||prevState.render!=this.state.render)
        

this.setState({
        lg:this.props.lg[this.props.Header.language],
        render:this.props.header.render,

})

}




loopsubpages = (type) => {

    if(type=="products")
    return this.props.pages.products.SubPages.map(page => 
        <PageListItem page={page} control={this.props.control} />
         )
    else if (type=="services")
    return this.props.pages.services.SubPages.map(page => 
        <PageListItem page={page} control={this.props.control} />
         )

    else return;
}

 listSubPages=(mainpage) =>{
  

    if(mainpage=="products" || mainpage=="services"){
       return <div><h1 style={{textAlign:this.props.Header.direction}}>{this.state.lg.ctrl.sbPg}</h1>
        <div class="mainPagesContainer">
{this.loopsubpages(mainpage)}
      </div></div>
    }

    else return;

}
  

  

    render() {
        return (
<div>
           <NavBar/>
           <center><h1>{this.state.lg.ctrl.cnpnl}</h1></center>
           <h1 style={{textAlign:this.props.Header.direction}}>{this.state.lg.ctrl.mnPg}</h1>

 <div class="mainPagesContainer">
           
           <PageListItem page={this.props.pages.products} control={this.props.control} />
           <PageListItem page={this.props.pages.services} control={this.props.control} />
           <PageListItem page={this.props.pages.contact} control={this.props.control} />
           <PageListItem page={this.props.pages.about} control={this.props.control} />
           <PageListItem page={this.props.pages.reserve} control={this.props.control} />
           <PageListItem page={this.props.pages.cart} control={this.props.control} />

           </div> 
          {this.listSubPages(this.props.control.activePageToControl)}


          <ControlInput />


            </div>
);}

}


const mapStateToProps = state => ({


    control: state.submit.pages.control,

     pages: state.submit.pages,
     Header: state.submit.Header.style,
     lg:state.submit.languages,
     header: state.submit.Header,
     UserData:state.submit.UserData,

});






 export default connect(mapStateToProps,{changecontrolpage,getInitialData})(AdminPanel);