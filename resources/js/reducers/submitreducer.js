import {clear_reservation,clear_purchase,loading,get_csrf,form_response,register_user,login_user,get_initial_data,submit_new_product,submit_new_page,submit_new_request,submit_new_activsubepage,submit_new_activepage,submit_new_page_config} from '../actions/types';

import reduxJSON from "./StateJSONTree"

 const initialState = reduxJSON;

// async function getInitialState(){

//   // await fetch("/jsondata")
//   //   .then(response => response.json())
//   //   .then(json => {return json;});

//   const response = await fetch('/jsondata');
// return await response.json();
// // console.log(json)
// // const initialState=json
// // return json;

// } 

// initialState=getInitialState();
// console.log(setTimeout(()=>{return initialState}, 1000));



export default (state = JSON.parse(theinitialstate), { type, payload }) => {
    switch (type) {

        case submit_new_product:
        return { ...state, ...payload }

        case submit_new_page:
        return { ...state, ...payload }

        case submit_new_request:
        return { ...state, ...payload }

        case submit_new_activepage:
           return {...state,
             pages:{...state.pages,
               control:{...state.pages.control,
                activePageToControl:payload}}}
                
        case form_response:
          if(payload.ordersreceived)
          return {...state,
            pages:{...state.pages,
              cart:{...state.pages.cart,
                ...payload}}}
          else if(payload.reservationsreceived)
          return {...state,
            pages:{...state.pages,
              reserve:{...state.pages.reserve,
                ...payload}}}
        
        case login_user:
          return {...state,
            UserData:{...state.UserData,...payload}}
        
        case register_user:
          return {...state,
            UserData:{...state.UserData,...payload}}
                        
        case get_csrf:
          return {...state,
            UserData:{...state.UserData,...payload}}

            case clear_purchase:
              return {...state,
                pages:{...state.pages,
                  cart:{...state.pages.cart,
                    clearPerchase:payload}}}
            break;

            case clear_reservation:
              return {...state,
                pages:{...state.pages,
                  reserve:{...state.pages.reserve,
                    clearReservation:payload}}}
            break;

            


          
          case submit_new_activsubepage:
              return {...state,
                pages:{...state.pages,
                  control:{...state.pages.control,
                    activeSubpageToControl:payload}}}
                    
                    
          case get_initial_data:
            return {...state,...payload}

          case loading:
            return {...state,Header:{...state.Header,loading:payload}}

          case submit_new_page_config:
              // console.log(payload.page)
              // console.log(payload.data)

          

          switch (payload.page) {
            case "contact":
                return {...state,
                  pages:{...state.pages,
                    contact:{...state.pages.contact,
                      ...payload.data}}}
              break;
            case "about":
                  return {...state,
                    pages:{...state.pages,
                      about:{...state.pages.about,
                        ...payload.data}}}
                break;
            case "UserData":
              return {...state,UserData:{...state.UserData,
                    ...payload.data}}
            break;
            case "reserve":
                  return {...state,
                    pages:{...state.pages,
                      reserve:{...state.pages.reserve,
                        ...payload.data}}}
                break;
            
                case "header":
                  return {...state,
                    Header:{...state.Header,...payload.data}}
                break;

                case "cart":
                  return {...state,
                    pages:{...state.pages,
                      cart:{...state.pages.cart,
                        ...payload.data}}}
                break;
                case "products":
                  return {...state,
                    pages:{...state.pages,
                      products:{...state.pages.products,
                        ...payload.data}}}
                break;
                case "services":
                    return {...state,
                      pages:{...state.pages,
                        services:{...state.pages.services,
                          ...payload.data}}}
                  break;
            
          
            default:
              break;
          }
              



    default:
        return state;
    }
}
