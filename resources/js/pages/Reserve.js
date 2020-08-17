import React, { Component } from 'react';
import {connect} from 'react-redux';
import {changePageConfiguration,changestatus,formsend} from "../actions/submitaction"

import FormPopUp from "./components/FormPopUp"

import Header from "./components/Header"
import NavBar from "./components/NavBar"
import Footer from "./components/Footer"
//import Loader from "../Social media Icons/loader.gif";

// import {fetchcontacts,fetchtasks,fetchsupplies,fetchteams,fetchalldata} from '../actions/getactions';
import "./Reserve.css"

class Reserve extends Component {

  constructor(props) {
    super(props);

    this.allAppointmentOptions=this.allAppointmentOptions.bind(this);
    this.ConfirmAppointment=this.ConfirmAppointment.bind(this)
    this.onexitchange=this.onexitchange.bind(this);
    this.getService=this.getService.bind(this);
    this.changeOrderView=this.changeOrderView.bind(this);
    this.reservationStatus=this.reservationStatus.bind(this);
    this.clearReservations=this.clearReservations.bind(this);
    this.showDetails=this.showDetails.bind(this);
    this.checkWholeDay=this.checkWholeDay.bind(this);
    this.dropchanged=this.dropchanged.bind(this);
    this.pagenumber=this.pagenumber.bind(this);


    var justifycontentdirection=(this.props.Header.direction=="left")?'flex-start':'flex-end';

    this.state={
      products:this.props.product,
      services:this.props.service,
      cart: this.props.cart,
      reserve: this.props.reserve,
      pagenumber:1,
      filterby:"all",
      perpage:30,
      ButtonDisplay:null,
      reservationData:[],
      ordersDisplay:[],
      lg:this.props.lg[this.props.Header.language],
      formPopUp:false,
      paymentPopUp:false,
      lasttotalprice:0,
      lasttotalitems:0,
      lastid:null,
      showDetails:false,
      orderDetails:[],
      directionStylecolumn:{
        textAlign:this.props.Header.direction,
        flexDirection:'column',
        display:'flex',
        flexWrap: 'wrap',
        alignContent:justifycontentdirection,
        justifyContent:justifycontentdirection,
      },
      directionStylerow:{
        textAlign:this.props.Header.direction,
        flexDirection:this.props.Header.flxdir,
        display:'flex',
        flexWrap: 'wrap',
        margin:'5px',
        
      },
      
    }

    
  }
 componentDidMount(){
  if(this.props.reserve.reservationsreceived&&this.props.reserve.reservations.length>0){
     var displayVar=[]
     console.log("orders received")
     this.props.reserve.reservations.map(reserve=>displayVar.push({display:"none"}));
     this.setState({ordersDisplay:displayVar})
     }
     this.setState({reserve: this.props.reserve})
 } 
componentDidUpdate(prevProps) {
  
  if(this.props.reserve.reservationsreceived&&this.props.reserve.reservations.length>0){
  var displayVar=[]
     console.log("orders received")
     this.props.reserve.reservations.map(reserve=>displayVar.push({display:"none"}));}
        var justifycontentdirection=(this.props.Header.direction=="left")?'flex-start':'flex-end';

if(JSON.stringify(this.props.reserve.reservations)!=JSON.stringify(this.state.reserve.reservations))
{
this.setState({
      products:this.props.product,
      services:this.props.service,
      cart: this.props.cart,
      reserve: this.props.reserve,
      ordersDisplay:displayVar,
      lg:this.props.lg[this.props.Header.language],
      directionStylecolumn:{
        textAlign:this.props.Header.direction,
        flexDirection:'column',
        display:'flex',
        flexWrap: 'wrap',
        alignContent:justifycontentdirection,
        justifyContent:justifycontentdirection,
      },
      directionStylerow:{
        textAlign:this.props.Header.direction,
        flexDirection:this.props.Header.flxdir,
        display:'flex',
        flexWrap: 'wrap',
      }
      
  })

}
this.clearReservations();
}

clearReservations(){
  if(this.props.reserve.clearReservation){

    let servicestate=this.state.services
  
    servicestate.Services.map(service=>{
      service.ClientAppointment.exists=false;
      service.ClientAppointment.Date='';
      service.ClientAppointment.Time='';
    })
    
    servicestate.SubPages.map(subpage=>{
      subpage.Services.map(service=>{
        service.ClientAppointment.exists=false;
        service.ClientAppointment.Date='';
        service.ClientAppointment.Time='';
      })
    })
     let reservestate=this.state.reserve
         reservestate.clearReservation=false;
    
      this.props.changePageConfiguration("reserve",reservestate)
      this.props.changePageConfiguration("services",servicestate)
      
      //remove popup form after receiving reservations
      this.setState({formPopUp:false})
      this.state.lastid=this.props.reserve.lastreservationid
      this.setState({paymentPopUp:true})
  
    
  }
}

checkWholeDay(weekappointments){
  var tstatus=false;
  var fstatus=true;
  weekappointments.map(appoint=>{
    if(appoint.exists)
          
    {
      if(appoint.WholeDay)
      {fstatus=false}
      else
      {tstatus=true;}    
    } 
  }) 

  return (tstatus)?tstatus:fstatus

}


reservationStatus=(reservaton)=>{
  this.props.changestatus(reservaton);
}

showDetails(details){

  this.setState({orderDetails:[
    {header:this.state.lg.rv.dt,placeholder:"",dataName:"date",dataValue:details.datetime,details:{},type:"text"},
    {header:this.state.lg.usr.usrNm,placeholder:"",dataName:"name",dataValue:details.name,details:{},type:"text"},
    {header:this.state.lg.usr.phone,placeholder:"",dataName:"telephone",dataValue:details.telephone,details:{},type:"text"},
    {header:this.state.lg.usr.Address,placeholder:"",dataName:"address",dataValue:details.address,details:{},type:"text"},
    {header:this.state.lg.usr.mail,placeholder:"",dataName:"email",dataValue:details.email,details:{},type:"text"},
    {header:this.state.lg.crt.notes,placeholder:"",dataName:"notes",dataValue:"",details:{},type:"textarea"},
    
  ]})

  this.setState({showDetails:true})

}

paymentchanged=(e)=>{
  let newcart=this.state.cart;
  newcart.PaymentMethodSelected=e.target.value;
  this.setState({cart:newcart})
}

dropchanged=(e,type)=>{
    
  this.setState({[type]:e.target.value})
  this.getreservations({filterby:this.state.filterby,pagenumber:1,
    perpage:this.state.perpage,[type]:e.target.value});
}

pagenumber=(e,page)=>{
  this.setState({pagenumber:page})
  this.getreservations({filterby:this.state.filterby,pagenumber:page,
    perpage:this.state.perpage});    
}

getreservations(data){
  var formdata = new FormData();
  formdata.append("filtering",true);
  formdata.append("filterby",data.filterby);
  formdata.append("pagenumber",data.pagenumber);
  formdata.append("perpage",data.perpage);
  
  this.props.formsend('newreservation','post',formdata);
  
}


 getService(id){
var serv2ret={};
    this.props.service.Services.map(service=>{
        if(service.ServiceId==id){
          serv2ret=service;} })

    this.props.service.SubPages.map(subpage=>{
      subpage.Services.map(service=>{
        if(service.ServiceId==id){
          serv2ret=service;} })})

    return serv2ret;
  }

  changeOrderView(e,orderIndex){
  if(this.props.reserve.reservationsreceived){
     var newone=this.state.ordersDisplay;
     (newone[orderIndex].display=="none")?
     newone[orderIndex]={display:'flex'}:newone[orderIndex]={display:'none'}
     this.setState({ordersDisplay:newone})
  }

    }

  ConfirmAppointment=(e)=>{
    
     
    this.setState({formPopUp:!this.state.formPopUp});
  }

  DropDownchange=(e,service)=>{
    var dButton=this.state.ButtonDisplay
    dButton[""+service.subpage+service.subPageIndex+service.index]="block";
    this.setState({ButtonDisplay:dButton})

    
let moment=require('moment')
let newState=this.state.services;

if(service.subpage){
  const newone=Object.assign({},newState.SubPages[service.subPageIndex].Services[service.index].ClientAppointment)
  newState.SubPages[service.subPageIndex].Services[service.index].OldClientAppointment=newone
newState.SubPages[service.subPageIndex].Services[service.index].ClientAppointment.Time=
moment(e.target.value.split("h")[0]+":"+e.target.value.split(":")[1].split("m")[0],"H:mm").format("H:mm");
}
else {
  const newone=Object.assign({},newState.Services[service.index].ClientAppointment)
  newState.Services[service.index].OldClientAppointment=newone
  
newState.Services[service.index].ClientAppointment.Time=
moment(e.target.value.split("h")[0]+":"+e.target.value.split(":")[1].split("m")[0],"H:mm").format("H:mm");
}
// console.log(newState)

this.setState({services:newState})

  }
  DateChange=(e,service,todate)=>{
    var dButton=this.state.ButtonDisplay
    dButton[""+service.subpage+service.subPageIndex+service.index]="block";
    this.setState({ButtonDisplay:dButton})
    var Date="Date"
    todate?Date="toDate":"Date"
    
    let moment=require('moment')
    console.log(e.target.value)
let newState=this.state.services;
if(service.subpage){
  const newone=Object.assign({},newState.SubPages[service.subPageIndex].Services[service.index].ClientAppointment)
  newState.SubPages[service.subPageIndex].Services[service.index].OldClientAppointment=newone
  newState.SubPages[service.subPageIndex].Services[service.index].ClientAppointment[Date]=moment(e.target.value,"YYYY/MM/DD").format("D/M/YYYY");
}
else {
  const newone=Object.assign({},newState.Services[service.index].ClientAppointment)
  newState.Services[service.index].OldClientAppointment=newone
  newState.Services[service.index].ClientAppointment[Date]=moment(e.target.value,"YYYY/MM/DD").format("D/M/YYYY");
}
this.setState({services:newState})
  

  }

  changeAppointmentButton=(service)=>{
    if (window.confirm('Are you sure you want to change your appointment?')) {
      let moment=require('moment') 
      let newState=this.state.services;
      var toDateexists=false

      const initialDate=moment(service.service.ClientAppointment.Date+" "+service.service.ClientAppointment.Time,"D/M/YYYY H:mm");
      
      var newservice=_.cloneDeep(this.setNewAppointment(initialDate,service))

      service.service=_.cloneDeep(newservice.newService)
      
      console.log("initial date ")
      console.log(initialDate)
      const InitialClientAppointment=_.cloneDeep(newservice.newService.ClientAppointment)
      console.log("initial service app ")
      console.log(InitialClientAppointment)

      if(!newservice.status){
        alert(newservice.reason)
        return;
      }
      

     
      if(service.service.ClientAppointment.hasOwnProperty("toDate")&&service.service.ClientAppointment.toDate!=null){//check if toDate is needed
        toDateexists=false
      const finalDate=moment(service.service.ClientAppointment.toDate+" "+service.service.ClientAppointment.Time,"D/M/YYYY H:mm");
      var newdate;
      var dates=[]
      var toDateStatus=false
      var daystatus;
      var serviceForNewDate;
      for(var i=0;i<=400;i++){
        newdate=initialDate.add(1, 'day');
        if(newdate.isSameOrBefore(finalDate)){
          console.log("new day is "+newdate.format('dddd'))
          for(let i=0;i<=6;i++){//search for day of the week that match new date and check if exists
          if(newdate.format('dddd')==service.service.Appointments[i].Day&&service.service.Appointments[i].exists){
              // dates.push(newdate)//add date to range
              console.log(newdate.format('dddd')+" "+service.service.Appointments[i].Day+" "+service.service.Appointments[i].exists)
              daystatus=true
          }
        }
        
        if(daystatus){
          daystatus=false
          console.log("new date is "+newdate.format('D/M/YYYY'))
          service.service=_.cloneDeep(newservice.newService)
          service.service.OldClientAppointment.exists==false//to avoind overrwritting in taken appointments
          console.log("previous service state ")
          console.log(newservice)

          serviceForNewDate=_.cloneDeep(this.setNewAppointment(newdate,service))          
          console.log("current service state ")
          console.log(serviceForNewDate)
         
          if(serviceForNewDate.status)
                   {toDateStatus=true;
                    newservice=_.cloneDeep(serviceForNewDate)
                    dates.push({Date:newdate.format('D/M/YYYY'),Time:newdate.format('H:mm')})
                    newservice.newService.ClientAppointment.toDate=newdate.format('D/M/YYYY');
                    // newservice.newService.
                    continue;
                  }
                    else {console.log("didn't succeed");break;}
                    
          } else{console.log(newdate.format('dddd')+" doesn't exists");continue;}
         
        }else{
          
          break;}
        
      }
    }
      // alert(service.service.ClientAppointment.toDate)

    console.log("hi there")
    console.log(newservice)
      newservice.newService.ClientAppointment={...newservice.newService.ClientAppointment,...InitialClientAppointment,Dates:dates}
      

      if(service.subpage){
        newState.SubPages[service.subPageIndex].Services[service.index]=newservice.newService;
        }
        else {
        newState.Services[service.index]=newservice.newService;
        }
        this.setState({services:newState})
        this.props.changePageConfiguration("services",this.state.services)

      
  } else {}
  }

  getNextTimeOption(chosenHour,chosenMin,increments){

    let nextMin=(Number(chosenMin)+Number(increments))%60
    let nextHour=(Number(chosenHour)+Math.floor((Number(chosenMin)+Number(increments))/60))%24
    let nextTime=nextHour+":"+((nextMin<=9) ? ("0"+nextMin) :nextMin);
  return {hour:nextHour,min:((nextMin<=9) ? ("0"+nextMin) :nextMin),time:nextTime}
  }

  allAppointmentOptions(service,date){
     
let optionsArray=[]
var moment=require('moment')
date=moment(date,"D/M/YYYY")
for(let i=0;i<=6;i++){
  
if(date.format('dddd')==service.Appointments[i].Day&&service.Appointments[i].exists){

    let startHour=service.Appointments[i].FromHour1;
    let startMin=service.Appointments[i].FromMin1;
    startMin=((startMin.length==1) ? ("0"+startMin) :startMin)
    startHour=((startHour=="00") ? 0 :startHour)
    let nextTime={time:startHour+":"+startMin,hour:startHour,min:startMin}
    let HourThisIteration=nextTime.hour
    let Selecttag
    let Taken=false


    while(nextTime.hour<24 && nextTime.hour>=HourThisIteration  
    ){
      console.log("entered while")
console.log(nextTime)
      HourThisIteration=nextTime.hour;
      Taken=false
      for(let l=0;l<service.TakenAppointments.length;l++){

        if(service.TakenAppointments[l].Time==nextTime.time && 
                service.TakenAppointments[l].Date==date.format('D/M/YYYY') &&
                service.TakenAppointments[l].number>=service.Appointments[i].ServingLines) {
                  Taken=true
                  
                  break
                }
      }

      

      if(Taken){
        nextTime=this.getNextTimeOption(nextTime.hour, nextTime.min, service.Appointments[i].ServingTime)
      }
      else{
        if(
          (
          moment(date.format("M/D/YYYY")+" "+nextTime.hour+":"+nextTime.min,"M/D/YYYY H:m").isBetween(
              moment(date.format("M/D/YYYY")+" "+service.Appointments[i].FromHour1+":"+service.Appointments[i].FromMin1,"M/D/YYYY H:m"),
              moment(date.format("M/D/YYYY")+" "+service.Appointments[i].ToHour1+":"+service.Appointments[i].ToMin1,"M/D/YYYY H:m"), undefined, '[]' )
              ||
              moment(date.format("M/D/YYYY")+" "+nextTime.hour+":"+nextTime.min,"M/D/YYYY H:m").isBetween(
              moment(date.format("M/D/YYYY")+" "+service.Appointments[i].FromHour2+":"+service.Appointments[i].FromMin2,"M/D/YYYY H:m"),
              moment(date.format("M/D/YYYY")+" "+service.Appointments[i].ToHour2+":"+service.Appointments[i].ToMin2,"M/D/YYYY H:m") , undefined, '[]')
              )
              &&
              moment(date.format("M/D/YYYY")+" "+nextTime.hour+":"+nextTime.min,"M/D/YYYY H:m").isSameOrAfter(moment())
              
              ){
                       
              Selecttag= (nextTime.time==service.ClientAppointment.Time)?"selected":""
                if( moment(date.format("M/D/YYYY")+" "+nextTime.hour+":"+nextTime.min,"M/D/YYYY H:m").isSame(moment(service.ClientAppointment.Date+" "+service.ClientAppointment.Time,"D/M/YYYY H:m"))){
                  optionsArray.push(<option selected>{nextTime.hour}h:{nextTime.min}min</option>)

                }
                
                else{
                  optionsArray.push(<option>{nextTime.hour}h:{nextTime.min}min</option>)
                }
                nextTime=this.getNextTimeOption(nextTime.hour, nextTime.min, service.Appointments[i].ServingTime)


              }
              else nextTime=this.getNextTimeOption(nextTime.hour, nextTime.min, service.Appointments[i].ServingTime)

              }

      }
  
}
}
return optionsArray;
  }

  setNewAppointment(date,Service){
    let service=Service.service
    let servicelocation=Service

    var displaynone=this.state.ButtonDisplay
    displaynone[""+servicelocation.subpage+servicelocation.subPageIndex+servicelocation.index]="none"
    this.setState({ButtonDisplay:displaynone})

    var moment = require('moment');
    if(date.isBefore(moment())){alert("Selected date is in the past");
                                date=moment();}
    for(let i=0;i<=6;i++){
      if(date.format('dddd')==service.Appointments[i].Day&&service.Appointments[i].exists||service.UnavailableDates.includes(date.format('D/M/YYYY'))){
  
    
      let startHour=date.format('H');
      let startMin=date.format('m');
      startMin=((startMin<=9&&startMin.length<2) ? ("0"+startMin) :startMin)
      let nextTime={time:startHour+":"+startMin,hour:startHour,min:startMin}

      for(let l=0;l<service.TakenAppointments.length;l++){
  //console.log(nextTime)
        if((service.TakenAppointments[l].Time==nextTime.time && 
            service.TakenAppointments[l].Date==date.format('D/M/YYYY') &&
            service.TakenAppointments[l].number>=service.Appointments[i].ServingLines) 
          || 
          moment().isSameOrAfter(moment(date.format("M/D/YYYY")+" "+nextTime.hour+":"+nextTime.min,"M/D/YYYY H:m")) 
          ||
          !(moment(date.format("M/D/YYYY")+" "+nextTime.hour+":"+nextTime.min,"M/D/YYYY H:m").isBetween(
            moment(date.format("M/D/YYYY")+" "+service.Appointments[i].FromHour1+":"+service.Appointments[i].FromMin1,"M/D/YYYY H:m"),
            moment(date.format("M/D/YYYY")+" "+service.Appointments[i].ToHour1+":"+service.Appointments[i].ToMin1,"M/D/YYYY H:m") )
             ||
            moment(date.format("M/D/YYYY")+" "+nextTime.hour+":"+nextTime.min,"M/D/YYYY H:m").isBetween(
            moment(date.format("M/D/YYYY")+" "+service.Appointments[i].FromHour2+":"+service.Appointments[i].FromMin2,"M/D/YYYY H:m"),
            moment(date.format("M/D/YYYY")+" "+service.Appointments[i].ToHour2+":"+service.Appointments[i].ToMin2,"M/D/YYYY H:m") ))
          ||
            !(l==service.TakenAppointments.length-1) ) 
          {if(l==service.TakenAppointments.length-1) alert("Chosen time is not availble")}
          
         else {
          for(let m=0;m<service.TakenAppointments.length;m++){
            // console.log("our m is "+m)
            // console.log(service.TakenAppointments[m].Time)
            // console.log(service.TakenAppointments[m].Date)
            // console.log(service.OldClientAppointment.Time)
            //   console.log(service.OldClientAppointment.Date)  
            
            // console.log(service.TakenAppointments.length-1)
            

        
            if((service.TakenAppointments[m].Time==service.OldClientAppointment.Time && 
                service.TakenAppointments[m].Date==service.OldClientAppointment.Date))
                {
                  if(service.TakenAppointments[m].number<=1)
                  {service.TakenAppointments[m].Time="";service.TakenAppointments[m].Date=""}
                  else {service.TakenAppointments[m].number--}
                }}
          
  
        //  if(service.TakenAppointments[l].number<service.Appointments[i].ServingLines&&service.TakenAppointments[l].number!=""&& service.TakenAppointments[l].Time==service.ClientAppointment.Time && service.TakenAppointments[l].Date==service.ClientAppointment.Date)
         if(service.TakenAppointments[l].number<service.Appointments[i].ServingLines&&service.TakenAppointments[l].number!=""&& service.TakenAppointments[l].Time==nextTime.time && service.TakenAppointments[l].Date==date.format('D/M/YYYY'))
                {service.TakenAppointments[l].number++}
          else service.TakenAppointments.push({Date:date.format('D/M/YYYY'),Time:nextTime.time,number:1})
          
          service.ClientAppointment.Date=date.format('D/M/YYYY')
          service.ClientAppointment.Time=nextTime.time
          service.ClientAppointment.exists=true
          alert("Appointment changed successfuly")

          
          
          return {status:true,newService:service}
         }
   
      }
     
      
      return {status:false,currentDate:date,reason:"We were unable to find any appointment in that day"}
    }
    else {
      // alert("Chosen date is not available is not available")
    }
   
    }
    return {status:false,currentDate:date,reason:"No appointments are available in "+date.format('dddd')}
  

  }

  onexitchange=(signal)=>{
    this.setState({formPopUp:signal})
    this.setState({paymentPopUp:signal})
    this.setState({showDetails:signal})

      }

componentWillMount(){
  var bDisplay={};
  this.state.services.Services.map((serviceInMain,serviceIndex)=>
  serviceInMain.ClientAppointment.exists ? 
    bDisplay["false"+"null"+serviceIndex]="none"
  
   : null
      );
  this.state.services.SubPages.map((Subpage,Subpage_Index)=>
  Subpage.Services.map((ServiceInSubpage,ServiceIndexInSubpage)=>
  ServiceInSubpage.ClientAppointment.exists ?
  bDisplay["true"+Subpage_Index+ServiceIndexInSubpage]="none"
 
  : null

  )
      ); 

      this.setState({ButtonDisplay:bDisplay})
 
}


    render() {

      let currentreservations=[];
      var totalPrice=0;
      

      var formarray=[
        {header:this.state.lg.usr.usrNm,placeholder:"",dataName:"name",dataValue:this.props.usrData.name,details:{},type:"general"},
        {header:this.state.lg.usr.phone,placeholder:"",dataName:"telephone",dataValue:this.props.usrData.otherTelephone,details:{},type:"general"},
        {header:this.state.lg.usr.Address,placeholder:"",dataName:"address",dataValue:this.props.usrData.otherAddress,details:{},type:"general"},
        {header:this.state.lg.usr.mail,placeholder:"",dataName:"email",dataValue:this.props.usrData.email,details:{},type:"general"},
        {header:this.state.lg.rv.notes,placeholder:this.state.lg.rv.notes,dataName:"notes",dataValue:"",details:{},type:"textarea"},
       
      ]


      var moment=require('moment')
      var ServicesWithAppointments=[];
      this.state.services.Services.map((serviceInMain,serviceIndex)=>
      serviceInMain.ClientAppointment.exists ? ServicesWithAppointments.push({service:serviceInMain,subpage:false,index:serviceIndex,subPageIndex:null}) : null
          );
      this.state.services.SubPages.map((Subpage,Subpage_Index)=>
      Subpage.Services.map((ServiceInSubpage,ServiceIndexInSubpage)=>
      ServiceInSubpage.ClientAppointment.exists ? ServicesWithAppointments.push({service:ServiceInSubpage,subpage:true,index:ServiceIndexInSubpage,subPageIndex:Subpage_Index}) : null

      )
          ); 
          ServicesWithAppointments.map(service=>{
            totalPrice=totalPrice+service.service.price;
            this.state.lasttotalprice=totalPrice;
            this.state.lasttotalitems=this.state.lasttotalitems+1;
            var tempToDate={};
            (service.service.ClientAppointment.hasOwnProperty("toDate")&&service.service.ClientAppointment.toDate!=null)?tempToDate={toDate:service.service.ClientAppointment.toDate,Dates:service.service.ClientAppointment.Dates}:null
                 currentreservations.push(
                   {serviceid:service.service.ServiceId,
                    servicename:service.service.ServiceName,
                    date:service.service.ClientAppointment.Date,
                    option:service.service.options.map(option=>(option.selected)?option.OptionName:null),
                    ...tempToDate,
                    time:service.service.ClientAppointment.Time})}
                 )   
              

          var moment = require('moment');

          console.log(ServicesWithAppointments)
      
     return (
            
          <div> 
              <Header />
            <NavBar />
              
              <h2 style={{textAlign:this.props.Header.direction}}>{this.state.reserve.PageName}</h2>   
              

              <div class="AppointmentsArea" style={this.state.directionStylerow}>
{
  ServicesWithAppointments.map( appointment=>

    <div class="servicesAppointments" >
<div>{appointment.service.ServiceName}</div>
<div>{appointment.service.options.map(option=>(option.selected)?option.OptionName:null)}</div>
<div style={this.state.directionStylerow}><div>{this.state.lg.rv.prc}</div> <div>:</div> &nbsp; <div>{appointment.service.price}</div></div>

{/* <div style={this.state.directionStylerow}><div>{this.state.lg.rv.dt}</div><div>:</div> &nbsp; <div> <input onChange={(e)=>this.DateChange(e,appointment)} type="date" min={moment().format("YYYY-MM-DD")} value={moment(appointment.service.ClientAppointment.Date,"D/M/YYYY").format("YYYY-MM-DD")}  /></div></div> */}

{
  (!this.checkWholeDay(appointment.service.Appointments))
   ?<div>
    <div style={this.state.directionStylerow}><div>{this.state.lg.rv.from}</div><div>:</div> &nbsp; <div> <input onChange={(e)=>this.DateChange(e,appointment,false)} type="date" min={moment().format("YYYY-MM-DD")} value={moment(appointment.service.ClientAppointment.Date,"D/M/YYYY").format("YYYY-MM-DD")}  /></div></div>
    <div style={this.state.directionStylerow}><div>{this.state.lg.rv.to}</div><div>:</div> &nbsp; <div> <input onChange={(e)=>this.DateChange(e,appointment,true)} type="date" min={moment().format("YYYY-MM-DD")} value={moment(appointment.service.ClientAppointment[appointment.service.ClientAppointment.hasOwnProperty("toDate")?"toDate":"Date"],"D/M/YYYY").format("YYYY-MM-DD")}  /></div></div>
    </div>
   :<div style={this.state.directionStylerow}><div>{this.state.lg.rv.dt}</div><div>:</div> &nbsp; <div> <input onChange={(e)=>this.DateChange(e,appointment,false)} type="date" min={moment().format("YYYY-MM-DD")} value={moment(appointment.service.ClientAppointment.Date,"D/M/YYYY").format("YYYY-MM-DD")}  /></div></div>

    
}

<div style={this.state.directionStylerow}><div>{this.state.lg.rv.tm}</div><div> :</div> &nbsp; <div><select onChange={(e)=>this.DropDownchange(e,appointment)}>
  {this.allAppointmentOptions(appointment.service,appointment.service.ClientAppointment.Date)}
</select></div>


  </div>
  <div style={this.state.directionStylerow}>
  <button style={{display:this.state.ButtonDisplay[""+appointment.subpage+appointment.subPageIndex+appointment.index]}} onClick={()=>this.changeAppointmentButton(appointment)}>{this.state.lg.rv.chngappBt}</button>
</div>
  

    </div>

  )

  
}


              </div>
{(ServicesWithAppointments.length>0)?<div>
<div style={this.state.directionStylerow}>
<div>{this.state.lg.crt.pymthd} </div><select style={{margin:"7px"}} onChange={this.paymentchanged} ref={this.selectorRef} >
{this.props.cart.PaymentMethodOptions.map(option => 
(option.exists)? <option value={option.Type}> {this.state.lg.crt[option.Type]}</option>:null
 )}
 </select></div>
 
 <div style={this.state.directionStylerow}>
 <button onClick={(e)=>this.ConfirmAppointment(e)}>{this.state.lg.rv.cnfmBt}</button>
 </div>
<div style={{margin:"7px",...this.state.directionStylerow}}><div>{this.state.lg.crt.tlpr}</div><div>{" : "}</div> <div>{totalPrice}</div> </div>
     
</div>:<div style={this.state.directionStylerow}>{this.state.lg.rv.nothing}</div>}

 {(this.props.reserve.reservationsreceived)?<div><hr></hr><h2 style={this.state.directionStylerow}>{this.state.lg.rv.appointments}</h2></div>:null}

 {(this.props.reserve.reservationsreceived)?

<div>
<div style={this.state.directionStylerow}>

<div>{this.state.lg.rv.filter}</div>
  <div>
  <select style={{margin:"7px"}} onChange={(e)=>this.dropchanged(e,"filterby")} >
<option value="reserved"> {this.state.lg.rv.reserved}</option>
<option value="waiting"> {this.state.lg.rv.waiting}</option>
<option value="done"> {this.state.lg.rv.done}</option>
<option value="cancel"> {this.state.lg.rv.cancel}</option>
<option selected value="all"> {this.state.lg.rv.all}</option>
 </select>

  </div>

  <div>{this.state.lg.rv.itemnumber}</div>
  <div>
  <select style={{margin:"7px"}} onChange={(e)=>this.dropchanged(e,"perpage")} >
<option value='10'> 10</option>
<option selected value='30'> 30</option>
<option value='50'> 50</option>
<option value='100'> 100</option>
 </select>

  </div>
  
  </div>

</div>

:null}



{(this.props.reserve.reservationsreceived)?
this.props.reserve.reservations.slice(0).reverse().map((reservation,reservationIndex)=> 

   <div class="orderLine" style={this.state.directionStylecolumn}>


<div style={this.state.directionStylerow}><div class="ordercellheader">{this.state.lg.rv.appnumber}</div>&nbsp;<div class="ordercell">{reservation.clienreservationid}</div>&nbsp;<div style={{textDecoration: "underline",cursor:"pointer"}} class="ordercell" onClick={()=>this.showDetails(reservation)}>{this.state.lg.crt.details}</div></div>
<div style={this.state.directionStylerow}><div class="ordercellheader">{this.state.lg.crt.tlpr}</div>&nbsp;<div class="ordercell">{reservation.totalprice}</div></div>
<div style={this.state.directionStylerow}>
  <div class="ordercellheader">{this.state.lg.crt.pymthd}</div>&nbsp;
  <div class="ordercell">{this.state.lg.crt[reservation.paymentmethod.slice(1,-1)]}</div>&nbsp;
  <div class="ordercell">
    {(reservation.paymentstatus!=undefined&&(reservation.paymentstatus=="paid"||reservation.paymentstatus=="creditpaid"))?"("+this.state.lg.crt[reservation.paymentstatus]+")":null}
    {(reservation.paymentstatus!=undefined&&reservation.paymentstatus=='"pending"')?"("+this.state.lg.crt[reservation.paymentstatus.slice(1,-1)]+")":null}
  </div>
  <button 
  style={{display:(reservation.paymentstatus!=undefined&&reservation.paymentstatus!="paid"&&(this.props.usrData.userType=="owner"||this.props.usrData.userType=="manager"))
  ?"block":"none"}} 
  onClick={()=>this.reservationStatus({reservationid:reservation.reservationid,status:"paid"})} 
  >{this.state.lg.crt.paid}</button>
</div>
<div onClick={(e)=>this.changeOrderView(e,reservationIndex)} class="orderdrop" style={this.state.directionStylerow}><div>{this.state.services.PageName}</div><div>&nbsp;v&nbsp;</div></div>
<div class="ordercell" style={{...this.state.directionStylerow,...this.state.ordersDisplay[reservationIndex]}}>
  {reservation.reservations.map(reserve=>
<div class= "productsInOrders" style={{...this.state.directionStylecolumn}}>
  <div style={this.state.directionStylerow}><div class="orderImage"><img class="imageordertag" src={this.getService(reserve.serviceid).image}></img></div><div style={{margin:'3px',padding:'16px'}}>{reserve.servicename}</div></div>
  <div style={this.state.directionStylerow}><div class="ordercell">{reserve.option}</div></div>
  <div style={this.state.directionStylerow}><div class="ordercellheader">{this.state.lg.pr.prc}</div>&nbsp;<div class="ordercell">{this.getService(reserve.serviceid).price}</div></div>
  <div style={this.state.directionStylerow}><div class="ordercellheader">{this.state.lg.rv.dt}</div>&nbsp;<div class="ordercell">{reserve.date}</div></div>
  {reserve.hasOwnProperty("toDate")&&reserve.toDate!=null?<div style={this.state.directionStylerow}><div class="ordercellheader">{this.state.lg.rv.to}</div>&nbsp;<div class="ordercell">{reserve.toDate}</div></div>:null}
  <div style={this.state.directionStylerow}><div class="ordercellheader">{this.state.lg.rv.tm}</div>&nbsp;<div class="ordercell">{reserve.time}</div></div>


</div>
  )}
  </div>
  <div style={this.state.directionStylerow}><div class="ordercellheader">{this.state.lg.crt.status}</div>&nbsp;<div class="ordercell">{this.state.lg.rv[reservation.status]}</div></div>
  <div  style={this.state.directionStylerow}>
{(reservation.status=='reserved'&& (this.props.usrData.userType=="owner"||this.props.usrData.userType=="manager"))
?
  <div class="ordercell">
  <button onClick={()=>this.reservationStatus({reservationid:reservation.reservationid,status:"waiting"})}>{this.state.lg.rv.waiting}</button>
  </div>
:null}

  {((reservation.status=='waiting' || reservation.status=='reserved')&& (this.props.usrData.userType=="owner"||this.props.usrData.userType=="manager"))
? <div class="ordercell">
  <button onClick={()=>this.reservationStatus({reservationid:reservation.reservationid,status:"done"})}>{this.state.lg.crt.done}</button>
  </div>:null}

{(reservation.status=='reserved')
?<div style={this.state.directionStylerow}><div class="ordercell">
  <button onClick={()=>this.reservationStatus({reservationid:reservation.reservationid,status:"cancel"})}>{this.state.lg.crt.cancel}</button></div>
  </div>
:null}
 </div>



</div>

)
:null}

{(this.props.reserve.reservationsreceived)?
<center>
  {/* <spane>{this.state.pagenumber>0?"<<":null}</spane>&nbsp;&nbsp;
  <spane>{this.state.pagenumber>0?"<":null}</spane>&nbsp;&nbsp; */}
  <spane onClick={(e)=>this.pagenumber(e,this.state.pagenumber-3)} style={{cursor:"pointer"}}>{this.state.pagenumber-3>0?this.state.pagenumber-3:null}</spane>&nbsp;&nbsp;
  <spane  onClick={(e)=>this.pagenumber(e,this.state.pagenumber-2)} style={{cursor:"pointer"}}>{this.state.pagenumber-2>0?this.state.pagenumber-2:null}</spane>&nbsp;&nbsp;
  <spane  onClick={(e)=>this.pagenumber(e,this.state.pagenumber-1)} style={{cursor:"pointer"}}>{this.state.pagenumber-1>0?this.state.pagenumber-1:null}</spane>&nbsp;&nbsp;
  <spane  onClick={(e)=>this.pagenumber(e,this.state.pagenumber)} style={{cursor:"pointer"}}>{this.state.pagenumber>0?this.state.pagenumber:null}</spane>&nbsp;&nbsp;
  <spane  onClick={(e)=>this.pagenumber(e,this.state.pagenumber+1)} style={{cursor:"pointer"}}>{this.state.pagenumber+1}</spane>&nbsp;&nbsp;&nbsp;
  <spane  onClick={(e)=>this.pagenumber(e,this.state.pagenumber+2)} style={{cursor:"pointer"}}>{this.state.pagenumber+3}</spane>&nbsp;&nbsp;&nbsp;&nbsp;
  <spane onClick={(e)=>this.pagenumber(e,this.state.pagenumber+3)} style={{cursor:"pointer"}}>{this.state.pagenumber+6}</spane>
  {/* <spane>&gt;</spane>&nbsp;&nbsp;
   <spane>&gt;&gt;</spane> */}
</center>
:null}




 <Footer/>

 

 {this.state.formPopUp?
 
 <FormPopUp exit={this.onexitchange} formAction="POST" formURL="newreservation"
  payment={false} submitButton={true}
  data={formarray} title={this.state.lg.rv.reservation}
   hidden={[{name:"reservations",value:currentreservations},
            {name:"today",value:new Date().toLocaleString()},
            {name:"totalprice",value:totalPrice},
            {name:"paymentmethod",value:this.state.cart.PaymentMethodSelected},
            {name:"paymentstatus",value:'pending'},
          ]} 
    description={this.state.lg.rv.reservationdata}  />
  
  :""}

  {this.state.paymentPopUp&&(this.state.cart.PaymentMethodSelected=='criditCard'||this.state.cart.PaymentMethodSelected=='mobiletransfer'||this.state.cart.PaymentMethodSelected=='paypal')?
    <FormPopUp exit={this.onexitchange} formAction="POST" formURL="newpayment"
    payment={true}
    paymentapi={this.state.cart.creditapi}
    checkoutcurrency={this.state.cart.checkoutcurrency}
      data={[]}
      totalprice={this.state.lasttotalprice}
      totalitems={this.state.lasttotalitems}
      lastid={{type:'reservation',lastid:this.state.lastid}}
      pymentmthd={this.state.cart.PaymentMethodSelected}

      title={this.state.lg.crt.pymthd}
      hidden={[
            {name:"today",value:new Date().toLocaleString()},
            {name:"totalprice",value:this.state.lasttotalprice},
            {name:"totalitems",value:this.state.lasttotalitems},
            {name:"paymentmethod",value:this.state.cart.PaymentMethodSelected},
          ]} 
    submitButton={(this.props.cart.PaymentMethodSelected=='criditCard'||this.props.cart.PaymentMethodSelected=='paypal')?true:false}

    description={
      (this.props.cart.PaymentMethodSelected=='criditCard'||this.props.cart.PaymentMethodSelected=='paypal')?
      <div><div>{this.state.lg.crt[this.props.cart.PaymentMethodSelected]}</div><br></br>
        <div  style={{justifyContent: "center",...this.state.directionStylerow}}><div>{this.state.lg.crt.pleasepay}</div><div>{(this.state.lasttotalprice/this.state.cart.paypalexchangerate).toFixed(2)+" "+"="+this.state.lasttotalprice+"/"+this.state.cart.paypalexchangerate}</div><div> USD </div></div></div>
       :
       <div><div>{this.state.lg.crt[this.props.cart.PaymentMethodSelected]}</div><br></br>
      <div  style={{justifyContent: "center",...this.state.directionStylerow}}><div> {this.state.lg.crt.sendpayment} </div>&nbsp;<div> {this.state.lasttotalprice} </div>&nbsp;<div> {this.state.lg.crt.tophonepayment} </div>&nbsp;<div>{this.state.cart.phonetransfer}</div>
      {/* <div> {this.state.lg.crt.fromphone} </div><div>{this.state.cart.phonetransfer}</div> */}
      </div></div>

      }  
      
      />
  
  :""
  }

       {this.state.showDetails?
        <FormPopUp exit={this.onexitchange} formAction="POST" formURL="newreservation"
           data={this.state.orderDetails} title={this.state.lg.crt.details} payment={false} submitButton={false}
           hidden={[]}
           />
           :null}


 </div>
 
         
 );

}

}

const mapStateToProps = state => ({
    
    service: state.submit.pages.services,
    reserve: state.submit.pages.reserve,
    cart:state.submit.pages.cart,
    lg:state.submit.languages,
    Header:state.submit.Header.style,
    usrData:state.submit.UserData,

});

 export default connect(mapStateToProps,{changestatus,changePageConfiguration,formsend})(Reserve);