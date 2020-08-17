import {createStore,applyMiddleware} from 'redux';
import rootreducer from './reducers';
import thunk from 'redux-thunk';
// import initialstate from "./reducers/StateJSONTree.json"
import { composeWithDevTools } from 'redux-devtools-extension';



var initstat=window.theinitialstate;





// const initstat=initialstate;
const middleware=[thunk];



const store=createStore(
    rootreducer,
    initstat,

    composeWithDevTools(applyMiddleware(...middleware))
    
    )

    export default store;