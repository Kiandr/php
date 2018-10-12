import { PropTypes } from 'react';
import EventHeader from '../../EventHeader';

const propTypes = {
    lead: PropTypes.object.isRequired,
    report: PropTypes.string.isRequired,
    form_id: PropTypes.number.isRequired,
    timestamp: PropTypes.number.isRequired,
    hash:      PropTypes.string.isRequired,
    hideEvent: PropTypes.func.isRequired
};

const SellingHeader = ({ lead, report, form_id, timestamp, hash, hideEvent }) => {

    // Create Lead Link
    const leadLink = (<a href={lead.link}>
        {lead.name}
    </a>);
    
    // Create Header Body
    const body = (<div>
        {leadLink}
        {' filled out a '}
        <b>{report}</b>
    </div>);

    // Create Header Element
    return (<EventHeader 
        image={lead.image}
        defaultClass={lead.defaultClass}
        defaultText={lead.defaultText}
        body={body}
        timestamp={timestamp}
        id={form_id}
        mode='selling'
        hash={hash}
        hideEvent={hideEvent}
    />);
};

SellingHeader.propTypes = propTypes;

export default SellingHeader;