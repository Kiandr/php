import { PropTypes } from 'react';
import EventHeader from '../../EventHeader';

const propTypes = {
    lead: PropTypes.object.isRequired,
    form: PropTypes.string,
    form_id: PropTypes.number.isRequired,
    listing: PropTypes.object,
    timestamp: PropTypes.number.isRequired,
    hash:      PropTypes.string.isRequired,
    hideEvent: PropTypes.func.isRequired
};

const defaultProps  = {
    form: 'Inquiry',
    listing: null
};

const InquiryHeader = ({ lead, form, form_id, listing, timestamp, hash, hideEvent }) => {

    // Create Lead Link
    const leadLink = (<a href={lead.link}>
        {lead.name}
    </a>);

    // Inquiry
    let listingLink;
    let inquiryDescription = ' sent a ' + form;

    // Create Listing Link
    if (listing) {
        listingLink = (<a href={listing.link}>
            {listing.name}
        </a>);
        inquiryDescription += ' regarding ';
    } else {
        inquiryDescription += ' about selling a property';
    }


    // Create Header Body
    const body = (<div>
        {leadLink}
        {inquiryDescription}
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
        mode='inquiry'
        hash={hash}
        hideEvent={hideEvent}
    />);
};

InquiryHeader.propTypes = propTypes;
InquiryHeader.defaultProps = defaultProps;

export default InquiryHeader;