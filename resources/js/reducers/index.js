import { combineReducers } from 'redux';

import submitreducer from './submitreducer';
import getreducer from './getreducer';

export default combineReducers(
{
    submit: submitreducer,
    get: getreducer
}
);