import { PropTypes } from 'react';
import EventBody from '../../EventBody';
import DataList from '../../DataList';

const propTypes = {
    listing: PropTypes.object,
    form: PropTypes.object
};

const defaultProps  = {
    listing: null,
    form: {},
};

const ShowingBody = ({listing, form}) => {

    const bodyStyle = {
        fontSize: '14px',
        color: '#71707c',
        padding: '12px 0'
    };

    let formData = [];
    if (form){
        if (form.address) {
            formData.push({
                id: 'address',
                title: 'Address',
                value: form.address
            });
        }
        if (form.price) {
            formData.push({
                id: 'price',
                title: 'Price',
                value: form.price
            });
        }
    }

    let formTable = '';
    if (formData.length > 0) {
        formTable = <DataList
            data={formData}
        />;
    }
    
    // Build Listing Image & Description
    let listingImage, listingBody;
    if (listing) {
        listingImage = (<span className='fto'>
            <img 
                src={listing.image}
                alt={listing.name}
            />
        </span>);
        listingBody = (<div style={bodyStyle}>
            {listing.name + ' • MLS# ' + listing.mls}
        </div>);
    }

    // Build Body Element
    return (<EventBody>
        {listingImage}
        {listingBody}
        {formTable}
        <p>{form.comments}</p>
    </EventBody>);
};

ShowingBody.propTypes = propTypes;
ShowingBody.defaultProps = defaultProps;

export default ShowingBody;