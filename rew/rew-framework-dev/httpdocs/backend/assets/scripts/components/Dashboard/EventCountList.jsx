import { PropTypes } from 'react';
import EventCount from './EventCount';
import FILTERS from 'constants/Dashboard/filters';

const propTypes = {
    inquiriesCount: PropTypes.number,
    listingsCount:  PropTypes.number,
    messageCount:   PropTypes.number,
    registerCount:  PropTypes.number,
    sellingCount:  PropTypes.number,
    filtering:      PropTypes.oneOf(FILTERS.list),
    setFilter:      PropTypes.func.isRequired
};

const defaultProps  = {
    inquiriesCount: 0,
    listingsCount:  0,
    messageCount:   0,
    registerCount:  0,
    sellingCount:  0,
    filtering:      null
};

const EventCountList = ({ registerCount, inquiriesCount, listingsCount, messageCount, sellingCount, filtering, setFilter }) => {
    return <div className="inbox-summary">
        <EventCount 
            eventCount={registerCount}
            eventName="Pending Leads"
            setFilter={setFilter}
            filterMode={FILTERS.register}
            active={filtering === FILTERS.register}
        />
        <EventCount 
            eventCount={messageCount}
            eventName="Messages"
            setFilter={setFilter}
            filterMode={FILTERS.message}
            active={filtering === FILTERS.message}
        />
        <EventCount 
            eventCount={inquiriesCount}
            eventName="Inquiries"
            setFilter={setFilter}
            filterMode={FILTERS.inquiry}
            active={filtering === FILTERS.inquiry}
        />
        <EventCount 
            eventCount={listingsCount}
            eventName="Requested Showings"
            setFilter={setFilter}
            filterMode={FILTERS.showing}
            active={filtering === FILTERS.showing}
        />
        <EventCount 
            eventCount={sellingCount}
            eventName="Requested Valuations"
            setFilter={setFilter}
            filterMode={FILTERS.selling}
            active={filtering === FILTERS.selling}
        />
    </div>;
};

EventCountList.propTypes = propTypes;
EventCountList.defaultProps = defaultProps;

export default EventCountList;