import React, { Component } from 'react';
import { Route, Link, BrowserRouter as Router, Switch } from 'react-router-dom'
import {Provider} from 'react-redux';
import store from './store';
import Welcome   from "./pages/Welcome"
import AdminPanel   from "./pages/AdminPanel"
import Contact   from "./pages/Contact"
import AboutUS from "./pages/AboutUs"
import Products from "./pages/ProductPage" 
import Services from "./pages/ServicePage"
import Cart from "./pages/Cart"
import Reserve from "./pages/Reserve"
import Login from "./pages/Login"
import Register from "./pages/Register"
import Profile from "./pages/Profile"


// import {getInitialData} from "./actions/submitaction"
import JSONUpdate from "./pages/components/JSONUpdate"



// import Navigationbar from './pages/components/navbar';




    export default class Main extends Component {

      

componentWillMount()
{
  // return (<div>loading...</div>)
  // alert("component mounted")
  // this.props.getInitialData()
}   


      render() {
        return (
<Provider store={store}>
       
<JSONUpdate/>
<Router>
    <div>
 
  
<Switch>
      <Route path="/welcome" component={Welcome} />
      <Route path="/adminpanel" component={AdminPanel} />
      <Route path="/cart" component={Cart} />
      <Route path="/order/:order_id"  render={(props) => <Cart {...props} order_page={true}/>} />
      <Route path="/reserve" component={Reserve} />
      <Route path="/reservation/:reservation_id" render={(props) => <Reserve {...props} reservation_page={true}/>}/>
      <Route path="/login" component={Login} />
      <Route path="/register" component={Register} />
      <Route path="/profile" component={Profile} />
      <Route path="/contact" component={Contact} />
       <Route exact path="/products" component={Products} />
       <Route path="/products/:subpageurl" render={(props) => <Products {...props} subpage={true}/>} />
        <Route exact path="/services" component={Services} />
        <Route path="/services/:subpageurl" render={(props) => <Services {...props} subpage={true}/>} />
      <Route path="/about" component={AboutUS} />
      <Route path="/" component={Welcome} />


      {/* <Route path="/" component={Welcome} /> */}
      </Switch>
    </div>
  </Router>

</Provider> 
      
        );
      }

    }


  