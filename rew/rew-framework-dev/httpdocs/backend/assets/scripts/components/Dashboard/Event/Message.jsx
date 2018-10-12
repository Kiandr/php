import { PropTypes } from 'react';
import MessageHeader from './Message/MessageHeader';
import MessageBody from './Message/MessageBody';
import MessageFooter from './Message/MessageFooter';

const propTypes = {
    authId: PropTypes.number.isRequired,
    hash: PropTypes.string.isRequired,
    data: PropTypes.object.isRequired,
    timestamp: PropTypes.number.isRequired,
    hideEvent: PropTypes.func.isRequired
};

const Message = ({ authId, hash, data, timestamp, hideEvent }) => {

    // Build Event
    return <div className='mda'>
        <MessageHeader
            authId={authId}
            lead={data.lead}
            message_id={parseInt(data.message.id)}
            timestamp={timestamp}
            hash={hash}
            hideEvent={hideEvent}
        />
        <MessageBody
            message={data.message}
        />
        <MessageFooter
            hash={hash}
            lead={data.lead}
            message={data.message}
            hideEvent={hideEvent}
        />
    </div>;
};

Message.propTypes = propTypes;

export default Message;