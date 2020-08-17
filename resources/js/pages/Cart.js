import React, { Component } from 'react';
import {connect} from 'react-redux';
import {changePageConfiguration,changestatus,getInitialData,formsend} from "../actions/submitaction"
import FormPopUp from "./components/FormPopUp"
import axios from 'axios';

import Header from "./components/Header"
import NavBar from "./components/NavBar"
import Footer from "./components/Footer"
import "./Cart.css"

class Cart extends Component {

  constructor(props) {
    super(props);
    this.selectorRef = React.createRef();
    this.onexitchange=this.onexitchange.bind(this);
    this.getOption=this.getOption.bind(this);
    this.orderStatus=this.orderStatus.bind(this);
    this.getProduct=this.getProduct.bind(this);
    this.changeOrderView=this.changeOrderView.bind(this);
    this.clearPurchase=this.clearPurchase.bind(this);
    this.showDetails=this.showDetails.bind(this);
    this.dropchanged=this.dropchanged.bind(this);
    this.pagenumber=this.pagenumber.bind(this);

    var justifycontentdirection=(this.props.Header.direction=="left")?'flex-start':'flex-end';
    this.state={
      products:this.props.product,
      render:this.props.render,
      pagenumber:1,
      filterby:"all",
      perpage:30,
      // services:this.props.service,
      cart: this.props.cart,
      lg:this.props.lg[this.props.Header.language],
      formPopUp:false,
      paymentPopUp:false,
      lasttotalprice:0,
      lasttotalitems:0,
      lastid:null,
      showDetails:false,
      orderDetails:[],
      ordersDisplay:[],
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


    }

 

    
  }


  componentDidUpdate(prevProps) {
    if(JSON.stringify(this.props.cart.orders)!=JSON.stringify(this.state.cart.orders)||
    this.state.render!=this.props.render)
{
  console.log(this.props.cart)
  if(this.props.cart.ordersreceived&&this.props.cart.orders.length>0){
var displayVar=[]
     console.log("orders received")
     this.props.cart.orders.map(ord=>displayVar.push({display:"none"}))}
    //  this.setState({ordersDisplay:displayVar})

   var justifycontentdirection=(this.props.Header.direction=="left")?'flex-start':'flex-end';
  this.setState({
       products:this.props.product,
       render:this.props.render,
      cart: this.props.cart,
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
  // this.setState(this.state)
 

}

this.clearPurchase();


  }

  clearPurchase(){
    if(this.props.cart.clearPerchase){

      let productstate=this.state.products
    
      productstate.Products.map(prod=>{
    prod.cart.SubTotal=0
    prod.cart.SubTotalDisplay='none'
    prod.cart.QuantityToAddDisplay='none'
      })
      
      productstate.SubPages.map(subpage=>{
        subpage.Products.map(prod=>{
          prod.cart.SubTotal=0
          prod.cart.SubTotalDisplay='none'
          prod.cart.QuantityToAddDisplay='none'
        })
      })
      let cartstate=this.state.cart
      this.state.lasttotalprice=cartstate.PriceTotal
      this.state.lasttotalitems=cartstate.TotalItems
        cartstate.PriceTotal=0
        cartstate.TotalItems=0
        cartstate.clearPerchase=false;
      
        this.props.changePageConfiguration("cart",cartstate)
        this.props.changePageConfiguration("products",productstate)
    
      //remove popup form after receiving reservations
      this.setState({formPopUp:false})
      this.state.lastid=this.props.cart.lastorderid
      this.setState({paymentPopUp:true})
      
    }
  }

  orderStatus(order){
    this.props.changestatus(order);

  }

  showDetails(details){

    this.setState({orderDetails:[
      {header:this.state.lg.rv.dt,placeholder:"",dataName:"date",dataValue:details.datetime,details:{},type:"text"},
      {header:this.state.lg.usr.usrNm,placeholder:"",dataName:"name",dataValue:details.name,details:{},type:"text"},
      {header:this.state.lg.usr.phone,placeholder:"",dataName:"telephone",dataValue:details.telephone,details:{},type:"text"},
      {header:this.state.lg.usr.Address,placeholder:"",dataName:"address",dataValue:details.address,details:{},type:"text"},
      {header:this.state.lg.usr.mail,placeholder:"",dataName:"email",dataValue:details.email,details:{},type:"text"},
      {header:this.state.lg.crt.notes,placeholder:"",dataName:"notes",dataValue:details.notes,details:{},type:"textarea"},
      
    ]})

    this.setState({showDetails:true})

  }

  UpdateCartPage(){

    let totalprice=0;
    let totalquantity=0;
    this.props.product.Products.map( product =>{
      if(product.cart.SubTotal!=0) {
        totalquantity+=Number(product.cart.QuantityToAdd);
        totalprice+=Number(product.cart.QuantityToAdd)*Number(product.price);
      }
    }
    )
  
    this.props.product.SubPages.map( product =>
  
      product.Products.map( product =>{
        if(product.cart.SubTotal!=0) {
        totalquantity+=Number(product.cart.QuantityToAdd)
        totalprice+=Number(product.cart.QuantityToAdd)*Number(product.price)}
      }
      
      )
    )
    let cartstate=this.state.cart
    cartstate.PriceTotal=totalprice
    cartstate.TotalItems=totalquantity
    console.log("price total is")
    console.log(totalprice)
    //this.setState({cart:cartstate})
  
    this.props.changePageConfiguration("cart",cartstate)
  
  }
  


  paymentchanged=(e)=>{
    let newcart=this.state.cart;
    newcart.PaymentMethodSelected=e.target.value;
    this.setState({cart:newcart})
  }

  dropchanged=(e,type)=>{
    
    this.setState({[type]:e.target.value})
    this.getorders({filterby:this.state.filterby,pagenumber:1,
      perpage:this.state.perpage,[type]:e.target.value});
  }

  pagenumber=(e,page)=>{
    this.setState({pagenumber:page})
    this.getorders({filterby:this.state.filterby,pagenumber:page,
      perpage:this.state.perpage});    
  }

  getorders(data){
    var formdata = new FormData();
    formdata.append("filtering",true);
    formdata.append("filterby",data.filterby);
    formdata.append("pagenumber",data.pagenumber);
    formdata.append("perpage",data.perpage);
    
    this.props.formsend('neworder','post',formdata);
    
  }
  
  order=(e)=>{
    
    let newcart=this.state.cart;
    newcart.PaymentMethodSelected=this.selectorRef.current.value;
    this.setState({cart:newcart})
    this.setState({formPopUp:true})

  }

  quantity(event,productindex,pageindex){
    let products=this.state.products;
    if(pageindex==null){      
      products.Products[productindex].cart.QuantityToAdd=event.target.value>products.Products[productindex].cart.QuantityAvailable?products.Products[productindex].cart.QuantityAvailable:event.target.value;
      products.Products[productindex].cart.SubTotal=products.Products[productindex].cart.QuantityToAdd*products.Products[productindex].price
    }
    else{      
      products.SubPages[pageindex].Products[productindex].cart.QuantityToAdd=event.target.value>products.SubPages[pageindex].Products[productindex].cart.QuantityAvailable?products.SubPages[pageindex].Products[productindex].cart.QuantityAvailable:event.target.value;
      products.SubPages[pageindex].Products[productindex].cart.SubTotal=products.SubPages[pageindex].Products[productindex].cart.QuantityToAdd*products.SubPages[pageindex].Products[productindex].price
    }
    this.props.changePageConfiguration("products",products)
    this.UpdateCartPage()

  }
changeOrderView(e,orderIndex){
  if(this.props.cart.ordersreceived){
    var newone=this.state.ordersDisplay;
    (newone[orderIndex].display=="none")?
    newone[orderIndex]={display:'flex'}:newone[orderIndex]={display:'none'}
    this.setState({ordersDisplay:newone})
 }


}

  onexitchange=(signal)=>{
this.setState({formPopUp:signal})
this.setState({paymentPopUp:signal})
this.setState({showDetails:signal})
  }

  getProduct(id){
var pro2ret={};
    this.props.product.Products.map(product=>{
        if(product.ProductId==id){
          pro2ret=product;} })

    this.props.product.SubPages.map(subpage=>{
      subpage.Products.map(product=>{
        if(product.ProductId==id){
          pro2ret=product;} })})

    return pro2ret;
  }

  getOption(optionArray){
    var optionselected="";
    
    optionselected=optionArray.map(option=>{if(option.selected){optionselected=option.OptionName;return optionselected}});
    if(optionArray.length<=1){return optionArray[0].OptionName;}
    //  if(optionselected!=""||optionselected!=null){return optionselected;}else {return "NaN";}
  return optionselected;
}



 componentDidMount(){
  // alert(this.props.usrData.otherAddress)
if(this.props.cart.ordersreceived&&this.props.cart.orders.length>0){
     var displayVar=[]
     console.log("orders received")
     this.props.cart.orders.map(ord=>displayVar.push({display:"none"}))
     this.setState({ordersDisplay:displayVar})
     }
     this.setState({cart: this.props.cart})
 } 

//  componentWillMount(){
//    console.log(this.props.match.params.order_id)
//    if(this.props.order_page){
//   fetch('/jsondata/order/'+this.props.match.params.order_id).then(response=>response.json()).then(
//     // json=> console.log(json)
//   )

//    }
//  }


    render() {

      var orderData=[];

      var formarray=[
        {header:this.state.lg.usr.usrNm,placeholder:"",dataName:"name",dataValue:this.props.usrData.name,details:{},type:"general"},
        {header:this.state.lg.usr.phone,placeholder:"",dataName:"telephone",dataValue:this.props.usrData.otherTelephone,details:{},type:"general"},
        {header:this.state.lg.usr.Address,placeholder:"",dataName:"address",dataValue:this.props.usrData.otherAddress,details:{},type:"general"},
        {header:this.state.lg.usr.mail,placeholder:"",dataName:"email",dataValue:this.props.usrData.email,details:{},type:"general"},
        {header:this.state.lg.crt.notes,placeholder:this.state.lg.crt.notes,dataName:"notes",dataValue:"",details:{},type:"textarea"},
        {header:this.state.lg.crt.tlpr,placeholder:"",dataName:"ordertotal",dataValue:this.state.cart.PriceTotal,details:{},type:"text"}
      ]

      


      // var productIds=[];
      // var productPrice=[];
      // var ProductQtys=[];
      // var ProductSubtotal=[];
      var productsTotal=0;

      // payhere.
      
      
    return (
           
          <div> 

              <Header />
            <NavBar />

            
         
              <h2 style={{textAlign:this.props.Header.direction}}>{this.state.cart.PageName}</h2>   
                      
 <div class="cartproductswrapper">

   <div style={this.state.directionStylerow} >
 
 { this.props.product.Products.map((product,productIndex)=>
 { if(product.cart.SubTotal>0  ){

//  orderData.push(product.ProductId, product.price, product.cart.QuantityToAdd, product.cart.SubTotal); productsTotal=productsTotal+product.cart.SubTotal;
orderData.push({productid:product.ProductId,name:product.ProductName,price:product.price,quantity:product.cart.QuantityToAdd,
  subtotal:product.cart.SubTotal,  option:this.getOption(product.options)  })

 return <div class="orderitem" style={this.state.directionStylecolumn}>
   <div class="detail" style={this.state.directionStylerow}><div class="orderImage"><img class="imageordertag" src={this.getProduct(product.ProductId).image}></img></div><div class="detail">{product.ProductName}</div></div>
   <div  class="detail"> {this.getOption(product.options)} </div> 
 <div class="detail" style={this.state.directionStylerow}><div>{this.state.lg.crt.qty}</div><div>&nbsp;</div><div><input  type="number" style={{width:"3em"}} onChange={(e)=>this.quantity(e,productIndex,null)}  value={product.cart.QuantityToAdd}></input></div></div> 
  <div style={this.state.directionStylerow} class="detail"><div>{this.state.lg.crt.sbtl}</div><div>:</div><div>&nbsp;</div><div> {product.cart.SubTotal}</div></div></div> 



}
 }
 )}

{this.props.product.SubPages.map( (product,pageIndex) =>

product.Products.map( (product,productIndex) =>{
    { if(product.cart.SubTotal>0){ 

      orderData.push({productid:product.ProductId,name:product.ProductName,price:product.price,quantity:product.cart.QuantityToAdd,
        subtotal:product.cart.SubTotal,option:this.getOption(product.options)})

return <div class="orderitem" style={this.state.directionStylecolumn}>
  <div class="detail" style={this.state.directionStylerow}><div class="orderImage"><img class="imageordertag" src={this.getProduct(product.ProductId).image}></img></div><div class="detail">{product.ProductName}</div></div>
  <div  class="detail"> {this.getOption(product.options)} </div> 
 <div class="detail" style={this.state.directionStylerow}><div>{this.state.lg.crt.qty}</div><div>&nbsp;</div><div><input type="number" style={{width:"3em"}} onChange={(e)=>this.quantity(e,productIndex,pageIndex)}  value={product.cart.QuantityToAdd}></input></div></div>  
 <div style={this.state.directionStylerow} class="detail"><div>{this.state.lg.crt.sbtl}</div><div>:</div><div>&nbsp;</div><div> {product.cart.SubTotal}</div></div></div> 


}
       }

})

)


}

</div>

{
  (this.state.cart.PriceTotal==0) ? <h3  style={{textAlign:this.props.Header.direction}}>{this.state.lg.crt.nothing}</h3>
:  <div>
  
  
  <div style={{margin:"7px",...this.state.directionStylerow}}><div>{this.state.lg.crt.tlpr}</div><div>{" : "}</div> <div>{this.state.cart.PriceTotal}</div><div>&nbsp;{" | "}&nbsp;</div><div> {this.state.lg.crt.tlitm}</div><div>{" : "}</div><div>{this.state.cart.TotalItems}</div> </div>

<div style={{textAlign:this.props.Header.direction}}>
<div>{this.state.lg.crt.pymthd} </div><select style={{margin:"7px"}} onChange={this.paymentchanged} ref={this.selectorRef} >
{this.props.cart.PaymentMethodOptions.map(option => 
(option.exists)? <option value={option.Type}> {this.state.lg.crt[option.Type]}</option>:null
)}
 </select>

 <br></br>
 <button style={{margin:"7px"}} onClick={this.order}>{this.state.lg.crt.ordbtn}</button>
 <br></br>
 </div>
</div>

 

}

{(this.props.cart.ordersreceived)?<div><hr></hr><h2 style={this.state.directionStylerow}>{this.state.lg.crt.orders}</h2></div>:null}

{(this.props.cart.ordersreceived)?

<div>
<div style={this.state.directionStylerow}>

<div>{this.state.lg.rv.filter}</div>
  <div>
  <select style={{margin:"7px"}} onChange={(e)=>this.dropchanged(e,"filterby")} >
<option value="preparing"> {this.state.lg.crt.preparing}</option>
<option value="ondelivery"> {this.state.lg.crt.ondelivery}</option>
<option value="done"> {this.state.lg.crt.done}</option>
<option value="cancel"> {this.state.lg.crt.cancel}</option>
<option selected value="all"> {this.state.lg.rv.all}</option>
 </select>

  </div>

  <div>{this.state.lg.crt.itemnumber}</div>
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

{(this.props.cart.ordersreceived)?


this.state.cart.orders.slice(0).reverse().map((order,orderIndex)=> 

   <div class="orderLine" style={this.state.directionStylecolumn}>


<div style={this.state.directionStylerow}><div class="ordercellheader">{this.state.lg.crt.ordid}</div>&nbsp;<div class="ordercell">{order.clientorderid}</div>&nbsp;<div style={{textDecoration: "underline",cursor:"pointer"}} class="ordercell" onClick={()=>this.showDetails(order)}>{this.state.lg.crt.details}</div></div>
<div style={this.state.directionStylerow}><div class="ordercellheader">{this.state.lg.crt.tlpr}</div>&nbsp;<div class="ordercell">{order.totalprice}</div></div>
<div style={this.state.directionStylerow}><div class="ordercellheader">{this.state.lg.crt.pymthd}</div>&nbsp;
  <div class="ordercell">{this.state.lg.crt[order.paymentmethod.slice(1,-1)]}</div>&nbsp;
  <div class="ordercell">
    {(order.paymentstatus!=undefined&&(order.paymentstatus=="paid"||order.paymentstatus=="creditpaid"))?"("+this.state.lg.crt[order.paymentstatus]+")":null}
    {(order.paymentstatus!=undefined&&order.paymentstatus=='"pending"')?"("+this.state.lg.crt[order.paymentstatus.slice(1,-1)]+")":null}
  </div>
  
  <button 
  style={{display:
    (order.paymentstatus!=undefined&&order.paymentstatus!="paid"&&(this.props.usrData.userType=="owner"||this.props.usrData.userType=="manager"))
    ?"block":"none"}} 
  onClick={()=>this.orderStatus({orderid:order.orderid,status:"paid"})} 
  >{this.state.lg.crt.paid}</button>

</div>
<div onClick={(e)=>this.changeOrderView(e,orderIndex)} class="orderdrop" style={this.state.directionStylerow}><div>{this.state.lg.crt.orders}</div><div>&nbsp;v&nbsp;</div></div>
<div class="ordercell" style={this.state.directionStylerow}>
  {order.order.map(ord=>
<div class= "productsInOrders" style={{...this.state.directionStylecolumn,...this.state.ordersDisplay[orderIndex]}}>
  <div style={this.state.directionStylerow}><div class="orderImage"><img class="imageordertag" src={this.getProduct(ord.productid).image}></img></div><div style={{margin:'3px',padding:'16px'}}>{ord.name}</div></div>
  <div style={this.state.directionStylerow}><div class="ordercell">{ord.option}</div></div>
  <div style={this.state.directionStylerow}><div class="ordercellheader">{this.state.lg.crt.qty}</div>&nbsp;<div class="ordercell">{ord.quantity}</div></div>
  <div style={this.state.directionStylerow}><div class="ordercellheader">{this.state.lg.pr.prc}</div>&nbsp;<div class="ordercell">{ord.price}</div></div>
  <div style={this.state.directionStylerow}><div class="ordercellheader">{this.state.lg.crt.sbtl}</div>&nbsp;<div class="ordercell">{ord.subtotal}</div></div>
</div>
  )}
   
 
  </div>
  <div style={this.state.directionStylerow}><div class="ordercellheader">{this.state.lg.crt.status}</div>&nbsp;<div class="ordercell">{this.state.lg.crt[order.status]}</div>
  <div  style={this.state.directionStylerow}>
{((order.status=='preparing')&& (this.props.usrData.userType=="owner"||this.props.usrData.userType=="manager"))
?<div class="ordercell"><button onClick={()=>this.orderStatus({orderid:order.orderid,status:"ondelivery"})}>{this.state.lg.crt.ondelivery}</button></div>

:null}

{((order.status=='preparing'||order.status=='ondelivery')&& (this.props.usrData.userType=="owner"||this.props.usrData.userType=="manager"))
?  <div class="ordercell"><button onClick={()=>this.orderStatus({orderid:order.orderid,status:"done"})}>{this.state.lg.crt.done}</button></div>
:null
}

{(order.status=='preparing')
?<div class="ordercell"><button onClick={()=>this.orderStatus({orderid:order.orderid,status:"cancel"})}>{this.state.lg.crt.cancel}</button></div>
:null}
</div>

</div>
<br></br>
</div>)
:null}
{(this.props.cart.ordersreceived)?
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

 </div>
 


 <Footer/>
 
 
 

 {this.state.formPopUp?
 
 <FormPopUp exit={this.onexitchange} formAction="POST" formURL="neworder"
  data={formarray} title={this.state.lg.crt.yrorder} payment={false} submitButton={true}
  // totalprice={this.state.cart.PriceTotal}
  // totalitems={this.state.cart.TotalItems}

   hidden={[{name:"order",value:orderData},
            {name:"today",value:new Date().toLocaleString()},
            {name:"totalprice",value:this.state.cart.PriceTotal},
            {name:"paymentmethod",value:this.state.cart.PaymentMethodSelected},
            {name:"paymentstatus",value:'pending'},
          ]} 
  //  hidden2={{name:"totalprice",value:this.state.cart.PriceTotal}}
    description={this.state.lg.crt.confirmdata}  />
  
  :""}

  
{(this.state.paymentPopUp&&(this.state.cart.PaymentMethodSelected=='criditCard'||this.state.cart.PaymentMethodSelected=='mobiletransfer'||this.state.cart.PaymentMethodSelected=='paypal'))?
    <FormPopUp exit={this.onexitchange} formAction="POST" formURL="newpayment"
    payment={true}
      // submitButton={(this.props.cart.PaymentMethodSelected=='criditCard'||this.props.cart.PaymentMethodSelected=='paypal')?true:false}
    paymentapi={this.state.cart.creditapi}
    checkoutcurrency={this.state.cart.checkoutcurrency}
      data={[]} 
      totalprice={this.state.lasttotalprice}
      totalitems={this.state.lasttotalitems}
      lastid={{type:'order',lastid:this.state.lastid}}
      pymentmthd={this.state.cart.PaymentMethodSelected}

 
      title={this.state.lg.crt.pymthd}
      hidden={[
            {name:"today",value:new Date().toLocaleString()},
            {name:"totalprice",value:this.state.lasttotalprice},
            {name:"totalitems",value:this.state.lasttotalitems},
            {name:"paymentmethod",value:this.state.cart.PaymentMethodSelected},
          ]} 
    description={
      ((this.props.cart.PaymentMethodSelected=='criditCard'||this.props.cart.PaymentMethodSelected=='paypal')&&this.state.cart.creditapi=="paypal")?
      <div><div>{this.state.lg.crt[this.props.cart.PaymentMethodSelected]}</div><br></br>
        <div  style={{justifyContent: "center",...this.state.directionStylerow}}><div>{this.state.lg.crt.pleasepay}</div><div>{(this.state.lasttotalprice/this.state.cart.paypalexchangerate).toFixed(2)+" "+"="+this.state.lasttotalprice+"/"+this.state.cart.paypalexchangerate}</div><div> USD </div></div></div>
       : (this.props.cart.PaymentMethodSelected=='criditCard'&&this.state.cart.creditapi=="checkout")?"":
       (this.props.cart.PaymentMethodSelected=='mobiletransfer')?
       <div><div>{this.state.lg.crt[this.props.cart.PaymentMethodSelected]}</div><br></br>
      <div  style={{justifyContent: "center",...this.state.directionStylerow}}><div> {this.state.lg.crt.sendpayment} </div>&nbsp;<div> {this.state.lasttotalprice} </div>&nbsp;<div> {this.state.lg.crt.tophonepayment} </div>&nbsp;<div>{this.state.cart.phonetransfer}</div>
      </div></div>:null }  
      
      />:""}

      {this.state.showDetails?
        <FormPopUp exit={this.onexitchange} formAction="POST" formURL="neworder"
           data={this.state.orderDetails} title={this.state.lg.crt.details} payment={false} submitButton={false}
           hidden={[]}
           />
           :null}
  
  </div>      
 );


}

}

const mapStateToProps = state => ({
    product: state.submit.pages.products,
    // service: state.submit.pages.services,
    usrData:state.submit.UserData,
    cart: state.submit.pages.cart,
    lg:state.submit.languages,
    Header:state.submit.Header.style,
    render:state.submit.Header.render,
});

 export default connect(mapStateToProps,{changePageConfiguration,changestatus,getInitialData,formsend})(Cart);