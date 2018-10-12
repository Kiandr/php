import { PropTypes } from 'react';
import EventHeaderImage from './EventHeaderImage';
import EventHeaderText from './EventHeaderText';
import EventHeaderDismiss from './EventHeaderDismiss';

const propTypes = {
    image: PropTypes.string,
    defaultClass: PropTypes.string,
    defaultText: PropTypes.string,
    body: PropTypes.node,
    timestamp: PropTypes.number.isRequired,
    id: PropTypes.number.isRequired,
    mode: PropTypes.string.isRequired,
    hash:      PropTypes.string.isRequired,
    hideEvent: PropTypes.func.isRequired
};

const defaultProps  = {
    image: null,
    defaultClass: null,
    defaultText: null,
    body: null
};

const EventHeader = ({ image, defaultClass, defaultText, body, timestamp, id, mode, hash, hideEvent }) => {

    return <div className="mda__hd">
        <EventHeaderImage
            image={image}
            defaultClass={defaultClass}
            defaultText={defaultText}
        />
        <EventHeaderText
            body={body}
            timestamp={timestamp}
        />
        <EventHeaderDismiss
            id={id}
            mode={mode}
            hash={hash}
            hideEvent={hideEvent}
        />
    </div>;
};

EventHeader.propTypes = propTypes;
EventHeader.defaultProps = defaultProps;

export default EventHeader;