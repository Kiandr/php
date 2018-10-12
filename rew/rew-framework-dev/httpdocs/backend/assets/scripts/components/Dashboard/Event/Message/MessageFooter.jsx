import { PropTypes, PureComponent } from 'react';
import EventFooter from '../../EventFooter';
import Icon from '../../Icon';
import URLS from 'constants/urls';
import showSuccess from 'utils/showSuccess';
import showErrors from 'utils/showErrors';

const propTypes = {
    hash:      PropTypes.string.isRequired,
    lead:      PropTypes.object.isRequired,
    message:   PropTypes.object.isRequired,
    hideEvent: PropTypes.func.isRequired
};

class MessageFooter extends PureComponent {

    constructor(props) {
        super(props);
        this.state = {value: ''};

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(event) {
        this.setState({ value: event.target.value});
    }

    handleSubmit() {

        // AJAX Data
        const data = {
            id: this.props.lead.id,
            subject: 'RE: ' + this.props.message.subject,
            message: this.state.value,
            reply: true,
            replyTo: this.props.message.id,
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

                // Record inquiry response
                $.ajax({
                    url: `${URLS.backendAjax }dashboard.php?messageResponse`,
                    type: 'POST',
                    dataType: 'json',
                    data: {message: this.props.message.id}
                }).always(() => {

                    showSuccess(json.success);

                    // Remove Event
                    this.props.hideEvent(this.props.hash);
                });
            }
        }).fail(() => {
            showErrors([
                'An Error occurred while replying to this lead! Please try again later.'
            ]);
        });
    }

    render() {

        // Build Footer Element
        return (<EventFooter>
            <span className='input w1/1'>
                <input
                    type='text'
                    onChange={(e) => this.handleChange(e)}
                    placeholder='Write a response...'
                />
                <a
                    data-button='message-email'
                    className='btn btn--email'
                    onClick={() => this.handleSubmit()}
                >
                    <Icon icon="email" />
                </a>
            </span>
        </EventFooter>);
    }
}

MessageFooter.propTypes = propTypes;

export default MessageFooter;