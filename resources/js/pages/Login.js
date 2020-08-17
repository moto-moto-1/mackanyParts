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






class Login extends Component {

    constructor(props) {
        super(props);

        this.LoginButton=this.LoginButton.bind(this);
        this.lostpass=this.lostpass.bind(this);

    this.state={
        render:this.props.render,
        email:this.props.UserData.email,
        password:this.props.UserData.password,
        lg:this.props.lg[this.props.Header.language],
        LinesStyle:{display:"flex",flexFlow:"wrap "+this.props.Header.flxdir}
        }

    }

    lostpass=()=>{
        let email=this.state.email;
        if(!email.includes('@')){alert("please write your e-mail to send you new password");return;}
        var formdata = new FormData();
-
        formdata.append('email',this.state.email);
        formdata.append('_token',this.props.UserData.csrfToken);

        this.props.loginUser("forgotpassword",formdata);

    }

LoginButton = () =>{

    var formdata = new FormData();

    formdata.append('email',this.state.email);
    formdata.append('password',this.state.password);
    formdata.append('_token',this.props.UserData.csrfToken);

    this.props.loginUser("login",formdata);
    
}

componentDidUpdate(prevProps) {
    if (prevProps.UserData.signedin !== this.props.UserData.signedin) {
        if(this.props.UserData.signedin){

            axios.defaults.headers.common['Authorization'] = 'Bearer '+this.props.UserData.jwtToken;
            
            
            if(this.props.UserData.userType=="owner"||this.props.UserData.userType=="manager")this.props.history.push("/adminpanel")

            if(this.props.pages.products.exists)this.props.history.push("/products")
        else if(this.props.pages.services.exists)this.props.history.push("/services")
        else if(this.props.pages.services.exists)this.props.history.push("/contact")
        else if(this.props.pages.services.exists)this.props.history.push("/about")
        else this.props.history.push("/profile")
        
        }
    }

    if(this.state.render!=this.props.render){
        this.setState({
            render:this.props.render,
            email:this.props.UserData.email,
            password:this.props.UserData.password,
            lg:this.props.lg[this.props.Header.language],
            LinesStyle:{display:"flex",flexFlow:"wrap "+this.props.Header.flxdir}
        })
    }
  }
  componentWillUnmount(){
    axios.defaults.headers.common['Authorization'] = 'Bearer '+this.props.UserData.jwtToken;


      this.props.getInitialData();
  }

componentWillMount(){

    //this.props.fetchcontacts();
    // this.props.fetchalldata('none','none');
    
}

changeit=(id,event)=>{

if(id.field=="email"){this.setState({email:event})}
else if(id.field=="password"){this.setState({password:event})} else {}
}




    render() {
        return (

            <div>
                 <Header/>
            <NavBar/>
            <div class="loginContainer">
        
         <InputLine header={this.state.lg.usr.mail} placeholder={this.state.lg.usr.plcHUsrNm}  name="email"
         data={this.state.email} 
         details={{type:'email',pattern:'.+',title:'enter full mail address'}} 
         changevalue={(e)=>this.changeit({field:"email"},e)} 
         type="general"/>
<br/>
        <InputLine header={this.state.lg.usr.pass} placeholder={this.state.lg.usr.PlcHPass}
         data={this.state.password} 
         changevalue={(e)=>this.changeit({field:"password"},e)} 
         type="password"/>
              <div style={{textAlign:"center"}}> <button onClick={()=>this.LoginButton()}>{this.state.lg.usr.Lgn}</button></div>
               <center style={{fontWeight:"bolder"}}><Link  to="/register"> {this.state.lg.usr.Rgstr} </Link></center>
<br></br>
               <center onClick={()=>this.lostpass()} style={{fontWeight:"bolder",cursor:'pointer'}}>{this.state.lg.usr.lostpass} </center>


</div>



<Footer/>
            </div>
        )}





}

const mapStateToProps = state => ({
    
    lg:state.submit.languages,
    Header:state.submit.Header.style,
    pages:state.submit.pages,
    UserData:state.submit.UserData,
    render:state.submit.Header.render,
    
});


export default connect(mapStateToProps,{loginUser,getInitialData})(Login);