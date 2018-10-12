import { PropTypes } from 'react';
import EventHeader from '../../EventHeader';

const propTypes = {
    authId: PropTypes.number.isRequired,
    lead: PropTypes.object.isRequired,
    message_id: PropTypes.number.isRequired,
    timestamp: PropTypes.number.isRequired,
    hash:      PropTypes.string.isRequired,
    hideEvent: PropTypes.func.isRequired
};

const MessageHeader = ({ authId, lead, message_id, timestamp, hash, hideEvent }) => {

    // Create Lead Link
    const leadLink = (<a href={lead.link}>
        {lead.name}
    </a>);

    // Create Agent Link
    let agentLink = '', actionAction = '';
    if (lead.agent && lead.agent != 1) {
        if (lead.agent != authId) {
            actionAction = ' to ';
            agentLink = <a href={lead.agentLink}>
                {lead.agentName}
            </a>;
        } else {
            actionAction = ' to You';
        }
    }
    
    // Create Header Body
    const body = (<div>
        {leadLink}
        {' sent a Direct Message'}
        {actionAction}
        {agentLink}
    </div>);

    // Create Header Element
    return (<EventHeader 
        image={lead.image}
        defaultClass={lead.defaultClass}
        defaultText={lead.defaultText}
        body={body}
        timestamp={timestamp}
        id={message_id}
        mode='message'
        hash={hash}
        hideEvent={hideEvent}
    />);
};

MessageHeader.propTypes = propTypes;

export default MessageHeader;