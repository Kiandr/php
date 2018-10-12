import { PropTypes } from 'react';
import ShowingHeader from './Showing/ShowingHeader';
import ShowingBody from './Showing/ShowingBody';
import ShowingFooter from './Showing/ShowingFooter';

const propTypes = {
    hash: PropTypes.string.isRequired,
    data: PropTypes.object.isRequired,
    timestamp: PropTypes.number.isRequired,
    hideEvent: PropTypes.func.isRequired
};

const Showing = ({ hash, data, timestamp, hideEvent }) => {

    // Build Event
    return <div className='mda'>
        <ShowingHeader
            lead={data.lead}
            listing={data.listing}
            form_id={parseInt(data.form.id)}
            timestamp={timestamp}
            hash={hash}
            hideEvent={hideEvent}
        />
        <ShowingBody
            listing={data.listing}
            form={data.form}
        />
        <ShowingFooter
            hash={hash}
            lead={data.lead}
            form={data.form}
            hideEvent={hideEvent}
        />
    </div>;
};

Showing.propTypes = propTypes;

export default Showing;