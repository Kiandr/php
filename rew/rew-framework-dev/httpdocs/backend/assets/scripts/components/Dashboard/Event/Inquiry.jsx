import { PropTypes } from 'react';
import InquiryHeader from './Inquiry/InquiryHeader';
import InquiryBody from './Inquiry/InquiryBody';
import InquiryFooter from './Inquiry/InquiryFooter';

const propTypes = {
    hash: PropTypes.string.isRequired,
    data: PropTypes.object.isRequired,
    timestamp: PropTypes.number.isRequired,
    hideEvent: PropTypes.func.isRequired
};

const Inquiry = ({ hash, data, timestamp, hideEvent }) => {

    // Build Event
    return <div className='mda'>
        <InquiryHeader
            lead={data.lead}
            listing={data.listing}
            form={data.form.name}
            form_id={parseInt(data.form.id)}
            timestamp={timestamp}
            hash={hash}
            hideEvent={hideEvent}
        />
        <InquiryBody
            listing={data.listing}
            form={data.form}
            comment={data.form.comments}
        />
        <InquiryFooter
            hash={hash}
            lead={data.lead}
            listing={data.listing}
            form={data.form}
            hideEvent={hideEvent}
        />
    </div>;
};

Inquiry.propTypes = propTypes;

export default Inquiry;