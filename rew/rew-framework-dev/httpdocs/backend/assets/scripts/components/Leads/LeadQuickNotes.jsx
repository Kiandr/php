import { Component, PropTypes } from 'react';
import showSuccess from 'utils/showSuccess';
import showErrors from 'utils/showErrors';
import URLS from 'constants/urls';

class LeadQuickNotes extends Component {
    static propTypes = {
        lead: PropTypes.number.isRequired,
        notes: PropTypes.string.isRequired
    };
    constructor(props) {
        super(props);
        this.state = {
            editing: false,
            saving: false,
            notes: props.notes
        };
    }
    handleKeyPress = (e) => {
        const keyCode = e.which || e.keyCode;
        if (keyCode === 13) this.handleSaveNote(e);
    };
    handleNoteClick = () => {
        this.setState({
            editing: true
        }, () => {
            this.input.focus();
        });
    };
    handleSaveNote = (e) => {
        e.preventDefault();
        if (this.state.saving) return;
        const oldNotes = this.state.notes.trim();
        const newNotes = this.input.value.trim();
        if (oldNotes === newNotes) {
            this.setState({
                editing: false,
                notes: newNotes
            });
        } else {
            this.setState({
                saving: true
            }, () => {
                $.ajax({
                    url: `${URLS.backendAjax}json.php?action=notes`,
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        lead: this.props.lead,
                        notes: newNotes
                    }
                }).done(data => {
                    if (data && data.success) {
                        showSuccess(['Your changes have been saved.']);
                        this.setState({
                            saving: false,
                            editing: false,
                            notes: newNotes
                        });
                    } else {
                        showErrors(data.errors || ['Your changes could not be saved.']);
                        this.setState({
                            saving: false,
                            editing: false,
                            notes: oldNotes
                        });
                    }
                }).fail(() => {
                    showErrors(['An unexpected error has occurred.']);
                    this.setState({
                        saving: false,
                        editing: false,
                        notes: oldNotes
                    });
                });
            });
        }
    };
    render() {
        const { notes } = this.state;
        if (this.state.editing) {
            return <input
                className="w1/1"
                defaultValue={notes}
                disabled={this.state.saving}
                onBlur={this.handleSaveNote}
                onKeyPress={this.handleKeyPress}
                ref={(input) => this.input = input} //eslint-disable-line react/jsx-no-bind
            />;
        }
        return notes && notes.length > 0
            ? <div onDoubleClick={this.handleNoteClick} onTouchEnd={this.handleNoteClick}>{notes}</div>
            : <div onClick={this.handleNoteClick}><a>{'Add Quick Notes'}</a></div>;
    }
}

export default LeadQuickNotes;