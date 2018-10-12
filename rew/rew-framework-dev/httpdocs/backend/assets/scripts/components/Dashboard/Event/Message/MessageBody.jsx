import { PropTypes } from 'react';
import EventBody from '../../EventBody';

const propTypes = {
    message: PropTypes.object.isRequired
};

const MessageBody = ({message}) => {

    // Build Body Element
    return (<EventBody>
        <b>{message.subject}</b>
        <p>{message.body}</p>
    </EventBody>);
};

MessageBody.propTypes = propTypes;

export default MessageBody;