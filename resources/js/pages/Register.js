import React, { Component } from 'react';
import {connect} from 'react-redux';
// import {fetchcontacts,fetchtasks,fetchsupplies,fetchteams,fetchalldata} from '../actions/getactions';
import Header from "./components/Header"
import NavBar from "./components/NavBar"
import Footer from "./components/Footer"
import "./Login.css"
import InputLine from "./components/InputLine"
// import { Link, BrowserRouter,Redirect} from 'react-router-dom'
import {registerUser} from "../actions/submitaction"





class Register extends Component {

    constructor(props) {
        super(props);
        this.RegisterButton=this.RegisterButton.bind(this);
        this.formInput = React.createRef();

    this.state={
        name:this.props.UserData.name,
        render:this.props.render,
        password:this.props.UserData.password,
        telephone:this.props.UserData.Telephone,
        email:this.props.UserData.email,
        address:this.props.UserData.Address,
        lg:this.props.lg[this.props.Header.language],
        LinesStyle:{display:"flex",flexFlow:"wrap "+this.props.Header.flxdir}
        }

    }

    verifypass=(pass,vpass)=>{

        if(pass==vpass)return true;
        else{
        alert("passwords doesn't match");
        return false;
      }
    }

    

    formSubmit=(e)=>{
         e.preventDefault();
        // console.log(this.formInput.current);
        if(this.verifypass(e.target.elements[4].value,e.target.elements[5].value)){
        
            const ett=<form><input></input></form>

             var formdata = new FormData();

            formdata.append("name",e.target.elements[0].value);
            formdata.append('password',e.target.elements[4].value);
            formdata.append('password_confirmation',e.target.elements[5].value);
            formdata.append('email',e.target.elements[1].value);
            formdata.append('telephone',e.target.elements[2].value);
            formdata.append('address',e.target.elements[3].value);
            formdata.append('_token',this.props.UserData.csrfToken);

             this.props.registerUser(formdata);
    }

    }

RegisterButton=()=>{
    //  this.props.history.push("/login");
    console.log("submitted");


}

componentDidUpdate(prevProps) {
    if (prevProps.UserData.signedin !== this.props.UserData.signedin) {
        if(this.props.UserData.signedin){

            axios.defaults.headers.common['Authorization'] = 'Bearer '+this.props.UserData.jwtToken;
            
            if(this.props.pages.products.exists)this.props.history.push("/products")
        else if(this.props.pages.services.exists)this.props.history.push("/services")
        else this.props.history.push("/contact")
        
        }
    }

    
    if(this.state.render!=this.props.render){
        this.setState({
            name:this.props.UserData.name,
        render:this.props.render,
        password:this.props.UserData.password,
        telephone:this.props.UserData.Telephone,
        email:this.props.UserData.email,
        address:this.props.UserData.Address,
        lg:this.props.lg[this.props.Header.language],
        LinesStyle:{display:"flex",flexFlow:"wrap "+this.props.Header.flxdir}
            
        })
    }
  }

componentWillMount(){

    //this.props.fetchcontacts();
    // this.props.fetchalldata('none','none');
    
}

changeit=(id,event)=>{

if(id.field=="name"){this.setState({name:event.value})}
else if(id.field=="password"){this.setState({password:event.value})} 
else if(id.field=="email"){this.setState({email:event.value})} 
else if(id.field=="telephone"){this.setState({telephone:event.value})} 
else if(id.field=="address"){this.setState({address:event.value})} 

else {}
}




    render() {
        return (

            <div>
                 <Header/>
            <NavBar/>
            <div class="loginContainer">
                <form
                 onSubmit={this.formSubmit}
                //  action="/register" method="post"
                // ref={this.formInput}
                 > 
            <InputLine header={this.state.lg.usr.usrNm} placeholder="" name="name"
            details={{type:'text',pattern:'[a-zA-Z ]{5,25}',title:'At least 5 characters',required:true}}
         data={this.state.name} 
         changevalue={(e)=>this.changeit({field:"name"},e)} 
         type="general"/>
<br/>

            <InputLine header={this.state.lg.usr.mail} placeholder="" data={this.state.email} 
            details={{type:'email',pattern:'.+',title:'enter full mail address',required:true}} name="email"
         changevalue={(e)=>this.changeit({field:"email"},e)} 
         type="general"/>
<br/>
            <InputLine header={this.state.lg.usr.phone} placeholder="ex: +10123478965" data={this.state.telephone} 
           details={{type:'tel',pattern:phoneregex,required:true,
            title:'Telephone should be in the international format e.g +10123478965'
        }} name="telephone"
         changevalue={(e)=>this.changeit({field:"telephone"},e)} 
         type="general"/>
<br/>
            <InputLine header={this.state.lg.usr.Address} placeholder=""  name="address"
         data={this.state.address} 
         details={{type:'text',pattern:'.+',
         title:'enter full address'
        }} 
         changevalue={(e)=>this.changeit({field:"address"},e)} 
         type="general"/>
<br/>
        <InputLine header={this.state.lg.usr.pass} placeholder=""  name="password"
         data={this.state.password} 
         details={{type:'password',pattern:'(.{8,})',title:'At least 8 characters',required:true}}
         changevalue={(e)=>this.changeit({field:"password"},e)} 
         type="general"/>
         <InputLine header={this.state.lg.usr.Verpass} placeholder="" name="password_confirmation"
         data={this.state.password} 
         details={{type:'password',pattern:'(.{8,})',title:'At least 8 characters',required:true}}
         changevalue={(e)=>this.changeit({field:"password"},e)} 
         type="general"/>

<div style={{textAlign:"center"}}> <button type='submit' onClick={()=>this.RegisterButton()}>{this.state.lg.usr.Rgstr}</button></div>
</form>
</div>


<Footer/>
            </div>
        )}





}

const mapStateToProps = state => ({
    
    lg:state.submit.languages,
    Header:state.submit.Header.style,
    UserData:state.submit.UserData,
    pages:state.submit.pages,
    render:state.submit.Header.render,

    
});


export default connect(mapStateToProps,{registerUser})(Register);