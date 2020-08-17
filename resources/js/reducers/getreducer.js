import {get_products,get_pages,get_requests,get_contacts,get_all_data} from '../actions/types';
import reduxJSON from "./StateJSONTree"

const initialState =reduxJSON;



export default (state = initialState, { type, payload }) => {
    switch (type) {

        case get_products:
        return { ...state, pages: payload }

        case get_pages:
        return { ...state, pages: payload }

        case get_requests:
        return { ...state, requests: payload }

        case get_contacts:
        console.log("inside contacts reducers");
        console.log(payload);
        return { ...state, contacts: payload }

        case get_all_data:
        return { ...state, pages: payload.pages,products: payload.pages.products,
                           contacts: payload.contacts,requests: payload.requests }

    default:
        return state;
    }
}
