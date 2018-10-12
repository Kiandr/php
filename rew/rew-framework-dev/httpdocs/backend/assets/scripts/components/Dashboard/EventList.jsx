import { PropTypes } from 'react';
import Masonry from 'react-masonry-component';
import Event from './Event';
import FILTERS from 'constants/Dashboard/filters';

const propTypes = {
    authId:         PropTypes.number.isRequired,
    events:       PropTypes.array.isRequired,
    hideEvent:    PropTypes.func.isRequired,
    filtering:      PropTypes.oneOf(FILTERS.list)
};

const defaultProps  = {
    filtering:      null
};

const EventList = ({authId, events, hideEvent, filtering }) => {

    let filteredArray = events;
    if (filtering) {
        filteredArray = events.filter((event) => {
            return event.mode === filtering;
        });
    }

    const eventsArray = filteredArray.map((event) => (
        <div
            className='node marB grid-event'
            key={event.hash} 
            data-type={event.mode}
        >
            <Event
                authId={authId}
                hash={event.hash}
                data={event.data}
                mode={event.mode} 
                timestamp={event.timestamp}
                hideEvent={hideEvent}
            />
        </div>
    ));

    const options = {
        columnWidth: '.grid-sizer',
        gutter: '.gutter-sizer',
        itemSelector: '.grid-event',
        percentPosition: true
    };

    return <Masonry
        options={options}
        updateOnEachImageLoad={true}
    >
        <div className='grid-sizer' />
        <div className='gutter-sizer' />
        {eventsArray}
    </Masonry>;
};

EventList.propTypes = propTypes;
EventList.defaultProps = defaultProps;

export default EventList;