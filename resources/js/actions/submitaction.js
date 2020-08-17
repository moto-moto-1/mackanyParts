import axios from 'axios';
import {clear_reservation,clear_purchase,loading,login_user,register_user,form_response,get_csrf,submit_new_product,submit_new_page,submit_new_request,get_initial_data,submit_new_activsubepage,submit_new_activepage,submit_new_page_config} from '../actions/types';

import store from "../store";



export const changecontrolpage = (type,data) => dispatch => {
    
   if(type=="submit_new_activepage")
           { dispatch(
                            {
                                type: submit_new_activepage,
                                payload: data
                            }
                          
    );}
    else if(type=="submit_new_activsubepage")
          {  dispatch(
                            {
                                type: submit_new_activsubepage,
                                payload: data
                            }
                          
    );}
    
    
    
    
    };

    export const changePageConfiguration = (pageToChange,dataToChange) => dispatch => {
       
                { dispatch(
                                 {
                                     type: submit_new_page_config,
                                     payload: {page:pageToChange,data:dataToChange}
                                 }
                               
         );}
//  if (pageToChange=="cart")return;



         };


    
export const updateServerData = () => dispatch => {

    dispatch(
        {
            type: loading,
            payload: true
        });

    const state = store.getState();
    axios.post(`/jsondata`,
    {
        headers: {'Content-Type': 'application/json'},
        data: {...state.submit,Header:{...state.submit.Header,loading:false}}
    }
       
    
    ).then(function (response) {
            dispatch(
                            {
                                type: get_initial_data,
                                payload: response.data
                            }
                        
    );
   axios.defaults.headers.common['Authorization'] = 'Bearer '+response.data.UserData.jwtToken;


});

    

}


export const registerUser = (payload) => dispatch => {

  axios(
    {
        method: 'post',
        url: '/register',
        data: payload,
        headers: {'Content-Type': 'multipart/form-data' }
         
    }
    
    ).then(function (response) {
        if(response.data.signedin){ dispatch(
                            {
                                type: register_user,
                                payload: response.data
                            }
                        
    )
    alert("congratulations! you have been registered")

}else console.log(response.statusText);


}).catch((error) => {


    for (var key of Object.keys(error.response.data.errors)) {
        alert(error.response.data.errors[key])

    }
    // error.response.data.errors.map(erroritem=>
    //     console.log(erroritem)
    // );
});

}




export const loginUser = (jwturl,payload) => dispatch => {

    axios(
      {
          method: 'post',
          url: '/api/auth/'+jwturl,
          data: payload,
          headers: {'Content-Type': 'multipart/form-data' }
           
      }
      
      ).then(function (response) {
        //   console.log(JSON.parse(response.data))
             
           dispatch(
                              {
                                  type: login_user,
                                  payload: response.data
                              }
                             
                          
      );
      (response.data.signedin)?alert("congratulations! successful login"):null
    }).catch((error) => {

        if(jwturl!="login"){
            for (var key of Object.keys(error.response.data.errors)) {
                alert(error.response.data.errors[key])
            
            }}

       else { dispatch(
            {
                type: login_user,
                payload: {signedin:false}
            }

        
);
 console.log(error.response.data.error)
// for (var key of Object.keys(error.response.data)) {
//     alert(error.response.data.errors[key])

// }

 if(error.response.data.error=="Unauthorized"){alert("Kindly re-type your info again or register");}

        }

    });
        

  }
  






export const getInitialData = () => dispatch => {

    dispatch(
        {
            type: loading,
            payload: true
        });
    
axios.get(`/jsondata`,
{
    headers: {'Content-Type': 'application/json'},

}

).then(function (response) {
        dispatch(
                        {
                            type: get_initial_data,
                            payload: response.data
                        }
                    
);
axios.defaults.headers.common['Authorization'] = 'Bearer '+response.data.UserData.jwtToken;
});



};


export const formsend = (url,method,payload) => dispatch => {

    axios(
      {
          method: method,
          url: url,
          data: payload,
          headers: {'Content-Type': 'multipart/form-data' }
           
      }
      
      ).then(function (response) {
           dispatch(
                              {
                                  type: form_response,
                                  payload: response.data
                              }
                          
      )

        if(response.data.ordersreceived){
        dispatch({
            type: clear_purchase,
            payload: true
        })}
        if(response.data.reservationsreceived){
            dispatch({
                    type: clear_reservation,
                    payload: true
                })}
  
  
  }).catch((error) => {
  
  
      for (var key of Object.keys(error.response.data.errors)) {
          alert(error.response.data.errors[key])
  
      }
      
  });
  
  }
  

  export const changestatus = (order) => dispatch => {

    if(order.hasOwnProperty('reservationid')){
    axios({method: 'post',url: '/changereservationstatus',data: order,headers: {'Content-Type': 'application/json' }}
        ).then(function (response) { dispatch({type: form_response,payload: response.data })}
            ).catch((error) => { alert(error.response)});
    }

else if(order.hasOwnProperty('orderid')){
    axios({method: 'post',url: '/changeorderstatus',data: order,headers: {'Content-Type': 'application/json' }}
            ).then(function (response) { dispatch({type: form_response,payload: response.data })}
                ).catch((error) => { alert(error.response)});}
  }