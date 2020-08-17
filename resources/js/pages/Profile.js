import React, { Component } from 'react';
import {connect} from 'react-redux';
// import {fetchcontacts,fetchtasks,fetchsupplies,fetchteams,fetchalldata} from '../actions/getactions';
import Header from "./components/Header"
import NavBar from "./components/NavBar"
import Footer from "./components/Footer"
import "./Login.css"
import InputLine from "./components/InputLine"
import { Link, BrowserRouter,Redirect } from 'react-router-dom'
import {loginUser,getInitialData} from "../actions/submitaction"


class Profile extends Component {

    constructor(props) {
        super(props);

        this.InfoChangeButton=this.InfoChangeButton.bind(this);
        this.logout=this.logout.bind(this);
        this.formInput = React.createRef();

    this.state={
        name:this.props.UserData.name,
        password:this.props.UserData.password,
        telephone:this.props.UserData.Telephone,
        email:this.props.UserData.email,
        address:this.props.UserData.Address,
        oldpassword:'',
        password:'',
        password_confirmation:'',
        lg:this.props.lg[this.props.Header.language],
        LinesStyle:{display:"flex",flexFlow:"wrap "+this.props.Header.flxdir},
        render:this.props.header.render,
        }

    }

    componentDidUpdate(prevProps) {

        if(!this.props.UserData.signedin){
            this.props.history.push("/login")
        }

        axios.defaults.headers.common['Authorization'] = 'Bearer '+this.props.UserData.jwtToken;
console.log("update in profile happened with state "+this.state.render+" and props "+this.props.header.render);
if(this.state.render!=this.props.header.render){
    this.setState({
        render:this.props.header.render,
        name:this.props.UserData.name,
        password:this.props.UserData.password,
        telephone:this.props.UserData.Telephone,
        email:this.props.UserData.email,
        address:this.props.UserData.Address,
        oldpassword:'',
        password:'',
        password_confirmation:'',
        lg:this.props.lg[this.props.Header.language],
        LinesStyle:{display:"flex",flexFlow:"wrap "+this.props.Header.flxdir},
    })

}

    }

    componentWillUnmount(){
        this.props.getInitialData();
    }

componentWillMount(){

    if(this.props.UserData.signedin){

    }
    else {
        this.props.history.push("/login");
    }
    //this.props.fetchcontacts();
    // this.props.fetchalldata('none','none');
    
}
verifypass=(pass,vpass)=>{

    if(pass==vpass)return true;
    else{
    alert("passwords doesn't match");
    return false;
  }
}

logout(){
    this.props.loginUser("logout"); 
}

formSubmit=(e)=>{
     e.preventDefault();
    // console.log(this.formInput.current);
    
        

         var formdata = new FormData();

        formdata.append("name",e.target.elements[0].value);

        if(e.target.elements[4].value.length>5){
        formdata.append('password',e.target.elements[4].value);}
        else {formdata.append('password',e.target.elements[3].value);}

        if(e.target.elements[5].value.length>5){
            formdata.append('password_confirmation',e.target.elements[5].value);}
            else {formdata.append('password_confirmation',e.target.elements[3].value);}

        formdata.append('oldpassword',e.target.elements[3].value);
        formdata.append('telephone',e.target.elements[1].value);
        formdata.append('address',e.target.elements[2].value);
        formdata.append('_token',this.props.UserData.csrfToken);


         this.props.loginUser("changeUserData",formdata);


}

InfoChangeButton=()=>{
//  this.props.history.push("/login");
console.log("submitted");


}

componentWillMount(){

//this.props.fetchcontacts();
// this.props.fetchalldata('none','none');

}

changeit=(id,event)=>{

if(id.field=="name"){this.setState({name:event.value})}

else if(id.field=="email"){this.setState({email:event.value})} 
else if(id.field=="telephone"){this.setState({telephone:event.value})} 
else if(id.field=="address"){this.setState({address:event.value})} 
else if(id.field=="oldpassword"){this.setState({oldpassword:event.value})} 
else if(id.field=="password"){this.setState({password:event.value})} 
else if(id.field=="password_confirmation"){this.setState({password_confirmation:event.value})} 

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
            details={{type:'text',pattern:'(.{5,})',title:'At least 5 characters'}}
         data={this.state.name} 
         changevalue={(e)=>this.changeit({field:"name"},e)} 
         type="general"/>
<br/>

            <InputLine header={this.state.lg.usr.mail} placeholder="" data={this.state.email} 
            details={{type:'email',pattern:'.+',title:'enter full mail address'}} name="email"
         changevalue={(e)=>this.changeit({field:"email"},e)} 
         type="text"/>
<br/>
            <InputLine header={this.state.lg.usr.phone} placeholder="ex: +201501001211" data={this.state.telephone} 
           details={{type:'tel',pattern:"(\\+\\d{6,12})",title:'enter full phone'}} name="telephone"
         changevalue={(e)=>this.changeit({field:"telephone"},e)} 
         type="general"/>
<br/>
            <InputLine header={this.state.lg.usr.Address} placeholder=""  name="address"
         data={this.state.address} 
         details={{type:'text',pattern:'.+',title:'enter full mail address'}} 
         changevalue={(e)=>this.changeit({field:"address"},e)} 
         type="general"/>
<br/>
<InputLine header={this.state.lg.usr.pass} placeholder=""  name="oldpassword"
         data={this.state.oldpassword} 
         details={{type:'password',pattern:'.+',title:'Old password'}} 
         changevalue={(e)=>this.changeit({field:"oldpassword"},e)} 
         type="general"/>
<br/>

<center><h3>{this.state.lg.usr.passChng}</h3></center>
<InputLine header={this.state.lg.usr.nwpass} placeholder=""  name="password"
         data={this.state.password} 
         details={{type:'password',pattern:'.+',title:'password'}} 
         changevalue={(e)=>this.changeit({field:"password"},e)} 
         type="general"/>
<br/><InputLine header={this.state.lg.usr.nwpasscnfrm} placeholder=""  name="password_confirmation"
         data={this.state.password_confirmation} 
         details={{type:'password',pattern:'.+',title:'password_confirmation'}} 
         changevalue={(e)=>this.changeit({field:"password_confirmation"},e)} 
         type="general"/>
<br/>

        

<div style={{textAlign:"center"}}> <button type='submit' onClick={()=>this.InfoChangeButton()}>{this.state.lg.usr.infoChng}</button></div>
</form>

<div style={{textAlign:"center"}}> <button  onClick={()=>this.logout()}>{this.state.lg.usr.logout}</button></div>
</div>


<Footer/>
            </div>
        )}





}

const mapStateToProps = state => ({
    
    lg:state.submit.languages,
    Header:state.submit.Header.style,
    UserData:state.submit.UserData,
    header:state.submit.Header,
    
});


export default connect(mapStateToProps,{loginUser,getInitialData})(Profile);