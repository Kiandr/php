import { PropTypes, PureComponent } from 'react';
import EventFooter from '../../EventFooter';
import Icon from '../../Icon';
import URLS from 'constants/urls';
import showSuccess from 'utils/showSuccess';
import showErrors from 'utils/showErrors';

const propTypes = {
    hash:      PropTypes.string.isRequired,
    lead:      PropTypes.object.isRequired,
    form:      PropTypes.object.isRequired,
    hideEvent: PropTypes.func.isRequired
};

class ShowingFooter extends PureComponent {

    constructor(props) {
        super(props);
        this.state = {messaging: false, value: ''};

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    switchMode() {
        this.setState({ messaging: !this.state.messaging});
    }

    handleChange(event) {
        this.setState({ value: event.target.value});
    }

    handleSubmit() {

        const data = {
            id: this.props.lead.id,
            subject: 'Property Showing' + (this.props.form.address ? (': ' + this.props.form.address) : ''),
            message: this.state.value,
            leads : [this.props.lead.id]
        };

        // AJAX Call
        $.ajax({
            url: `${URLS.backendAjax }json.php?action=message`,
            type: 'POST',
            dataType: 'json',
            data: data
        }).done((json) => {

            if (json.errors) {
                showErrors(json.errors);
            } else if (json.success) {

                // Record showing request response
                $.ajax({
                    url: `${URLS.backendAjax }dashboard.php?eventResponse`,
                    type: 'POST',
                    dataType: 'json',
                    data: {event: this.props.form.id}
                }).always(() => {
                    showSuccess(json.success);

                    // Remove Event
                    this.props.hideEvent(this.props.hash);
                });
            }
        }).fail(() => {
            showErrors([
                'An Error occured while replying to this lead! Please try again later.'
            ]);
        });
    }

    render() {

        // Scheduling Showing
        if (!this.state.messaging) {

            // Build Footer Element
            return (<EventFooter>
                <div 
                    style={{float: 'none'}}
                    className='btns'
                >
                    <a
                        className='btn btn--email'
                        onClick={() => this.switchMode()}
                        style={{width: 'calc(50% - 12px)', float: 'right'}}
                    >
                        <Icon icon="email" />
                        {'Reply'}
                    </a>
                    <a
                        href={URLS.backend + 'leads/lead/reminders?id=' + encodeURIComponent(this.props.lead.id) + '&type=2&details=' + encodeURIComponent('RE: ' + this.props.form.comments) + '#add'}
                        className='btn btn--schedule'
                        style={{width: 'calc(50% - 12px)', margin: 0, float: 'left'}}
                    >
                        <Icon icon="calendar" />
                        {' Schedule'}
                    </a>
                </div>
            </EventFooter>);

        // Sending Message
        } else {

            // Build Footer Element
            return (<EventFooter>
                <span className='input w1/1'>
                    <input
                        type='text'
                        onChange={(e) => this.handleChange(e)}
                        placeholder='Write a response...'
                    />
                    <a
                        className='btn btn--email'
                        onClick={() => this.handleSubmit()}
                    >
                        <Icon icon="email" />
                    </a>
                </span>
            </EventFooter>);
        }
    }
}

ShowingFooter.propTypes = propTypes;

export default ShowingFooter;