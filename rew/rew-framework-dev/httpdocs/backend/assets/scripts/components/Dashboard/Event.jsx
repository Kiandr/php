import { PropTypes } from 'react';
import Inquiry from './Event/Inquiry';
import Message from './Event/Message';
import Register from './Event/Register';
import Selling from './Event/Selling';
import Showing from './Event/Showing';

const propTypes = {
    authId:         PropTypes.number.isRequired,
    hash: PropTypes.string.isRequired,
    mode: PropTypes.oneOf(['inquiry', 'register', 'message', 'showing', 'selling']).isRequired,
    data: PropTypes.object.isRequired,
    hideEvent: PropTypes.func.isRequired,
    timestamp: PropTypes.number.isRequired
};

const Event = ({authId, hash, data, mode, timestamp, hideEvent }) => {

    switch(mode) {
    case 'inquiry':
        return <Inquiry 
            hash={hash}
            data={data}
            timestamp={timestamp}
            hideEvent={hideEvent}
        />;
    case 'message':
        return <Message
            authId={authId}
            hash={hash}
            data={data}
            timestamp={timestamp}
            hideEvent={hideEvent}
        />;
    case 'register':
        return <Register 
            authId={authId}
            hash={hash}
            data={data}
            timestamp={timestamp}
            hideEvent={hideEvent}
        />;
    case 'selling':
        return <Selling 
            hash={hash}
            data={data}
            timestamp={timestamp}
            hideEvent={hideEvent}
        />;
    case 'showing':
        return <Showing 
            hash={hash}
            data={data}
            timestamp={timestamp}
            hideEvent={hideEvent}
        />;
    }
};

Event.propTypes = propTypes;

export default Event;