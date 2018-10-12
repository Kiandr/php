import { PureComponent, PropTypes } from 'react';
import EventList from './EventList';
import EventCountList from './EventCountList';
import EventListener from './EventListener';
import HeroPad from '../HeroPad';
import FILTERS from 'constants/Dashboard/filters';

const propTypes = {
    authId:         PropTypes.number.isRequired,
    events:         PropTypes.array.isRequired,
    loadingInitialEvents:  PropTypes.bool.isRequired,
    loadingMoreEvents: PropTypes.bool.isRequired,
    doneLoading:   PropTypes.bool.isRequired,
    loadEvents:     PropTypes.func.isRequired,
    hideEvent:      PropTypes.func.isRequired,
    inquiriesCount: PropTypes.number,
    listingsCount:  PropTypes.number,
    messageCount:   PropTypes.number,
    registerCount:  PropTypes.number,
    sellingCount:   PropTypes.number,
    filtering:      PropTypes.oneOf(FILTERS.list),
    setFilter:      PropTypes.func.isRequired
};

const defaultProps  = {
    inquiriesCount: 0,
    listingsCount:  0,
    messageCount:   0,
    registerCount:  0,
    sellingCount:   0,
    filtering:      null
};

class Dashboard extends PureComponent {

    getListener() {
        if (!this.props.doneLoading && !this.props.loadingMoreEvents && !this.props.loadingInitialEvents) {
            return <EventListener
                className='grid-event'
                loadEvents={() => this.props.loadEvents()}
            />;
        }
    }

    getBody() {
        if (this.props.events.length == 0) {

            const innerBody = <p className="text text--mute">
                {'Grab a coffee or a smoothie, time to get proactive and follow-up on some of those '}
                <a href="/backend/leads/">{'leads'}</a>
                {'!'}
            </p>;

            return <HeroPad
                image={'/backend/img/ills/coffee.png'}
                header={'Looks Like You\'re all Caught Up!'}
                body={innerBody}
            />;
        } else {
            return <EventList
                authId={this.props.authId}
                events={this.props.events}
                hideEvent={this.props.hideEvent}
                filtering={this.props.filtering}
            />;
        }
    }

    render() {

        return <div>
            <div id="timeline">
                <EventCountList
                    inquiriesCount={this.props.inquiriesCount}
                    listingsCount={this.props.listingsCount}
                    messageCount={this.props.messageCount}
                    registerCount={this.props.registerCount}
                    sellingCount={this.props.sellingCount}
                    filtering={this.props.filtering}
                    setFilter={this.props.setFilter}
                />
            </div>
            {this.getBody()}
            {this.getListener()}
        </div>;
    }
}

Dashboard.propTypes = propTypes;
Dashboard.defaultProps = defaultProps;

export default Dashboard;


