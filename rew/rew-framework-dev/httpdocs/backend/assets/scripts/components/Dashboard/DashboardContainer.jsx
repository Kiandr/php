import { PropTypes, Component } from 'react';
import Dashboard from './Dashboard';
import URLS from 'constants/urls';
import FILTERS from 'constants/Dashboard/filters';

const propTypes = {
    authId:           PropTypes.number.isRequired,
    events:           PropTypes.array.isRequired,
    timestamp:        PropTypes.number.isRequired,
    nextUnloadedMessage:  PropTypes.string,
    nextUnloadedRegister: PropTypes.string,
    nextUnloadedInquiry: PropTypes.string,
    nextUnloadedSelling: PropTypes.string,
    nextUnloadedShowing: PropTypes.string,
    unloadedMessageCount:     PropTypes.number,
    unloadedRegisterCount:    PropTypes.number,
    unloadedInquiryCount:     PropTypes.number,
    unloadedShowingCount:     PropTypes.number,
    unloadedSellingCount:     PropTypes.number
};

const defaultProps  = {
    nextUnloadedMessage:   null,
    nextUnloadedRegister:  null,
    nextUnloadedInquiry:   null,
    nextUnloadedSelling:   null,
    nextUnloadedShowing:   null,
    unloadedMessageCount:  0,
    unloadedRegisterCount: 0,
    unloadedInquiryCount:  0,
    unloadedShowingCount:  0,
    unloadedSellingCount:  0
};

class DashboardContainer extends Component {

    constructor (props) {
        super(props);

        // Timestamps and keys used to load additional events
        this.timestamp = props.timestamp;
        this.nextUnloadedMessage = props.nextUnloadedMessage;
        this.nextUnloadedRegister = props.nextUnloadedRegister;
        this.nextUnloadedInquiry = props.nextUnloadedInquiry;
        this.nextUnloadedSelling = props.nextUnloadedSelling;
        this.nextUnloadedShowing = props.nextUnloadedShowing;
        this.nextFilteredEvent = null;

        // Set Unloaded Events Count
        this.unloadedMessageCount = props.unloadedMessageCount;
        this.unloadedRegisterCount = props.unloadedRegisterCount;
        this.unloadedInquiryCount = props.unloadedInquiryCount;
        this.unloadedShowingCount = props.unloadedShowingCount;
        this.unloadedSellingCount = props.unloadedSellingCount;
        this.loadedFilteredCount = 0;

        // Set Loaded
        this.loadedMessages =  !props.nextUnloadedMessage;
        this.loadedRegister =  !props.nextUnloadedRegister;
        this.loadedInquiries = !props.nextUnloadedInquiry;
        this.loadedSellings =  !props.nextUnloadedSelling;
        this.loadedShowings =  !props.nextUnloadedShowing;
        this.loadedFiltered =  null;

        this.state = {
            events:       props.events,
            hiddenEvents: [],
            hiddenLeads: [],
            loadingInitialEvents: true,
            loadingMoreEvents: false,
            doneLoading: this.doneLoading(false),
            filtering: null,
            filteredEvents: []
        };
    }

    componentDidMount() {
        this.onMount();
    }

    onMount() {
        // Update State to indicate initial load is completed
        this.setState({'loadingInitialEvents': false});
        // Begin Polling
        setTimeout(() => this.pollEvents(), 20000);
    }

    /**
     * Hide completed event
     * @param string hash
     * @param int|null lead
     */
    hideEvent(eventHash, eventLead) {

        const hiddenEvents = this.state.hiddenEvents;
        const hiddenLeads = this.state.hiddenEvents;

        let newState = {}, updateState = false;
        if (hiddenEvents.indexOf(eventHash) === -1) {
            newState.hiddenEvents = hiddenEvents.concat(eventHash);
            updateState = true;
        }
        if (eventLead !==undefined && hiddenLeads.indexOf(eventLead) === -1) {
            newState.hiddenLeads = hiddenLeads.concat(eventLead);
            updateState = true;
        }

        if (updateState) {
            this.setState(newState);
        }
    }

    /**
     * Update Filter and State
     * @param string filterBy
     */
    setFilter (filterBy) {

        // Switch to new filter
        let newFilter = null;
        if (this.state.filtering === filterBy || FILTERS.list.indexOf(filterBy) === -1) {
            this.nextFilteredEvent = null;
        } else {
            newFilter = filterBy;
            if (newFilter == FILTERS.register) {
                this.nextFilteredEvent = this.nextUnloadedRegister;
                this.loadedFiltered = this.loadedRegister;
            } else if (newFilter == FILTERS.message) {
                this.nextFilteredEvent = this.nextUnloadedMessage;
                this.loadedFiltered = this.loadedMessages;
            } else if (newFilter == FILTERS.inquiry) {
                this.nextFilteredEvent = this.nextUnloadedInquiry;
                this.loadedFiltered = this.loadedInquiries;
            } else if (newFilter == FILTERS.showing) {
                this.nextFilteredEvent =this.nextUnloadedShowing;
                this.loadedFiltered = this.loadedShowings;
            } else if (newFilter == FILTERS.selling) {
                this.nextFilteredEvent = this.nextUnloadedSelling;
                this.loadedFiltered = this.loadedSellings;
            }
        }
        this.setState({ 
            filtering: newFilter,
            filteredEvents: [],
            doneLoading: this.doneLoading(newFilter ? true : false) 
        });

        // Reset Filter
        this.loadedFilteredCount = 0;
    }

    /**
     * Update Timestamp for futher queries
     * @param int rawTimestamp
     */
    updateTimestamp(rawTimestamp) {

        // Set New Timestamp
        const timestamp = parseInt(rawTimestamp);
        if (timestamp > this.timestamp) {
            this.timestamp = timestamp;
        }
    }

    /**
     * Update Event Counts and Cursors
     * @param object data
     * @param bool filter
     */
    updateUnloadedEvents(data, filter) {

        if (!filter) {
            this.nextUnloadedMessage = data.nextUnloadedMessage;
            this.nextUnloadedRegister = data.nextUnloadedRegister;
            this.nextUnloadedInquiry = data.nextUnloadedInquiry;
            this.nextUnloadedSelling = data.nextUnloadedSelling;
            this.nextUnloadedShowing = data.nextUnloadedShowing;
            this.unloadedMessageCount = this.unloadedMessageCount - data.loadedMessageCount;
            this.unloadedRegisterCount = this.unloadedRegisterCount - data.loadedRegisterCount;
            this.unloadedInquiryCount = this.unloadedInquiryCount - data.loadedInquiryCount;
            this.unloadedSellingCount = this.unloadedSellingCount - data.loadedSellingCount;
            this.unloadedShowingCount = this.unloadedShowingCount - data.loadedShowingCount;
            this.loadedMessages =  !data.nextUnloadedMessage;
            this.loadedRegister =  !data.nextUnloadedRegister;
            this.loadedInquiries = !data.nextUnloadedInquiry;
            this.loadedSellings =  !data.nextUnloadedSelling;
            this.loadedShowings =  !data.nextUnloadedShowing;
        } else {
            this.nextFilteredEvent = data.nextFilteredEvent;
            this.loadedFilteredCount = this.loadedFilteredCount + data.loadedFilteredCount;
            this.loadedFiltered = !data.nextFilteredEvent;
        }
    }

    /**
     * Update React Events State
     * @param array events
     * @param bool append Add to end of array
     */
    updateEvents(events, append) {

        // Get New Events
        const currentEvents = this.state.events.map(function(e) {return e.hash;});
        const isNotCurrentEvent = (e) => currentEvents.indexOf(e.hash) === -1;
        const newEvents = events.filter(isNotCurrentEvent);

        // Set New State
        if (newEvents.length > 0) {
            if (append) {
                this.setState(prevState => ({events: [...prevState.events, ...newEvents]}));
            } else {
                this.setState(prevState => ({events: [...newEvents, ...prevState.events]}));
            }
        }
    }

    /**
     * Update React Filtered Events State
     * @param array events
     */
    updateFilteredEvents(events) {

        // Get New Events
        const currentEvents = this.state.filteredEvents.map(function(e) {return e.hash;});
        const isNotCurrentEvent = (e) => currentEvents.indexOf(e.hash) === -1;
        const newEvents = events.filter(isNotCurrentEvent);

        // Set New State
        if (newEvents.length > 0) {
            this.setState(prevState => ({filteredEvents: [...prevState.filteredEvents, ...newEvents]}));
        }
    }

    /**
     * Run ajax call to get new events
     */
    pollEvents() {

        // Poll events since timestamp
        $.ajax({
            url: `${URLS.backendAjax }dashboard.php?updateNew`,
            type: 'GET',
            data: {
                timestamp: this.timestamp
            },
            dataType: 'json'
        }).done((data) => {
            if (!data.errors || data.errors.length === 0) {
                if (data.events.length === 0) data.events = [];
                this.updateTimestamp(data.timestamp, null);
                this.updateEvents(data.events, false);
            }
        });

        this.polling = setTimeout(() => this.pollEvents(), 20000);
    }

    /**
     * Run ajax call for events
     */
    loadEvents() {

        if (!this.state.doneLoading && !this.state.loadingMoreEvents) {
            this.setState({'loadingMoreEvents': true});

            // Get Data and URL
            let url = `${URLS.backendAjax }dashboard.php?fetchOldEvents`;
            let data = []; 
            if (!this.state.filtering) {
                data = {
                    nextUnloadedRegister: this.nextUnloadedRegister,
                    nextUnloadedMessage:  this.nextUnloadedMessage,
                    nextUnloadedInquiry:  this.nextUnloadedInquiry,
                    nextUnloadedShowing:  this.nextUnloadedShowing,
                    nextUnloadedSelling:  this.nextUnloadedSelling
                };
            } else {
                url = `${URLS.backendAjax }dashboard.php?fetchFilteredOldEvents`;
                data = {
                    filter:            this.state.filtering,
                    nextFilteredEvent: this.nextFilteredEvent
                };
            }

            $.ajax({
                url: url,
                type: 'GET',
                data: data,
                dataType: 'json'
            }).done((data) => {
                if (!data.errors || data.errors.length === 0) {
                    const isFiltering = this.state.filtering ? true : false;
                    this.updateUnloadedEvents(data, isFiltering);
                    if (isFiltering) {
                        this.updateFilteredEvents(data.events);
                    } else {
                        this.updateEvents(data.events, true);
                    }
                }
            }).always(() => {
                this.setState({
                    'loadingMoreEvents': false,
                    'doneLoading': this.doneLoading(this.state.filtering ? true : false)
                });
            });
        }
    }

    /**
     * Get a filtered array of events from state
     * @return array
     */
    getFilterEvents() {

        let events = this.state.events;

        // Include Filtered Events
        if (this.state.filtering) {
            const eventHashes = events.map(function(e) {return e.hash;});
            const isNotCurrentEvent = (e) => eventHashes.indexOf(e.hash) === -1;
            const filteredEvents = this.state.filteredEvents.filter(isNotCurrentEvent);
            events = events.concat(filteredEvents);
        }

        // Remove Addressed Events
        const hiddenEvents = this.state.hiddenEvents;
        const hiddenLeads = this.state.hiddenLeads;
        const isNotHiddenEvent = (e) => hiddenEvents.indexOf(e.hash) === -1 && hiddenLeads.indexOf(e.data.lead.id) === -1; 
        return events.filter(isNotHiddenEvent);
    }

    /**
     * Count events of a paticular type
     * @param array events
     * @param string mode
     * @return int
     */
    countEvents(events, mode) {

        if (!mode) return events.length;
        let count = 0;
        events.forEach(function(event) {
            if (event.mode === mode) count++;
        });
        if (mode === 'inquiry') count = count + this.unloadedInquiryCount;
        if (mode === 'showing') count = count + this.unloadedShowingCount;
        if (mode === 'message') count = count + this.unloadedMessageCount;
        if (mode === 'register') count = count + this.unloadedRegisterCount;
        if (mode === 'selling') count = count + this.unloadedSellingCount;

        if (this.state.filtering === mode) {
            count = count - this.loadedFilteredCount;
        }

        return count;
    }

    /**
     * Is the dashboard done loading old events
     * @param bool filtering
     * @return bool
     */
    doneLoading(filtering) {
        if (filtering) {
            return this.loadedFiltered;
        } else {
            return this.loadedRegister
                && this.loadedMessages
                && this.loadedInquiries
                && this.loadedShowings
                && this.loadedSellings;
        }
    }

    render() {

        // Get Visable Events
        const events = this.getFilterEvents();

        return <Dashboard 
            authId={this.props.authId}
            events={events}
            inquiriesCount={this.countEvents(events, 'inquiry')}
            listingsCount={this.countEvents(events, 'showing')}
            sellingCount={this.countEvents(events, 'selling')}
            messageCount={this.countEvents(events, 'message')}
            registerCount={this.countEvents(events, 'register')}
            loadingInitialEvents={this.state.loadingInitialEvents}
            loadingMoreEvents={this.state.loadingMoreEvents}
            doneLoading={this.state.doneLoading}
            hideEvent={(e, l) => this.hideEvent(e, l)}
            loadEvents={() => this.loadEvents()}
            filtering={this.state.filtering}
            setFilter={(filter) => this.setFilter(filter)}
        />;
    }
}

DashboardContainer.propTypes = propTypes;
DashboardContainer.defaultProps = defaultProps;

export default DashboardContainer;