import { PropTypes } from 'react';
import EventHeader from '../../EventHeader';

const propTypes = {
    authId: PropTypes.number.isRequired,
    lead: PropTypes.object.isRequired,
    register_id: PropTypes.number.isRequired,
    timestamp: PropTypes.number.isRequired,
    hash:      PropTypes.string.isRequired,
    hideEvent: PropTypes.func.isRequired
};

const RegisterHeader = ({authId, lead, register_id, timestamp, hash, hideEvent }) => {

    // Create Lead Link
    const leadLink = (<a href={lead.link}>
        {lead.name}
    </a>);

    let action = ' has registered';
    let agentLink = '';
    if (lead.agent != 1 && lead.agent != authId) {
        action = ' is waiting for acceptance from ';
        agentLink = (<a href={lead.agentLink}>
            {lead.agentName}
        </a>);
    } else if (lead.agent == 1 && lead.agent == authId && lead.status != 'unassigned') {
        action = ' is waiting for acceptance from ';
        let agentName = ' You';
        agentLink = (<a href={lead.agentLink}>
            {agentName}
        </a>);
    } 

    // Create Header Body
    const body = (<div>
        {leadLink}
        {action}
        {agentLink}
    </div>);

    // Create Header Element
    return (<EventHeader 
        image={lead.image}
        defaultClass={lead.defaultClass}
        defaultText={lead.defaultText}
        body={body}
        timestamp={timestamp}
        id={register_id}
        mode='register'
        hash={hash}
        hideEvent={hideEvent}
    />);
};

RegisterHeader.propTypes = propTypes;

export default RegisterHeader;