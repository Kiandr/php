import { PropTypes } from 'react';
import EventHeader from '../../EventHeader';

const propTypes = {
    lead: PropTypes.object.isRequired,
    listing: PropTypes.object,
    form_id: PropTypes.number.isRequired,
    timestamp: PropTypes.number.isRequired,
    hash:      PropTypes.string.isRequired,
    hideEvent: PropTypes.func.isRequired
};

const defaultProps  = {
    listing: null,
};

const ShowingHeader = ({ lead, listing, form_id, timestamp, hash, hideEvent }) => {

    // Create Lead Link
    const leadLink = (<a href={lead.link}>
        {lead.name}
    </a>);
    
    // Create Listing Link
    let listingLink = 'a listing';
    if (listing) {
        listingLink = (<a href={listing.link}>
            {listing.name}
        </a>);
    }
    
    // Create Header Body
    const body = (<div>
        {leadLink}
        {' requested a showing of '}
        {listingLink}
    </div>);

    // Create Header Element
    return (<EventHeader 
        image={lead.image}
        defaultClass={lead.defaultClass}
        defaultText={lead.defaultText}
        body={body}
        timestamp={timestamp}
        id={form_id}
        mode='showing'
        hash={hash}
        hideEvent={hideEvent}
    />);
};

ShowingHeader.propTypes = propTypes;
ShowingHeader.defaultProps = defaultProps;

export default ShowingHeader;