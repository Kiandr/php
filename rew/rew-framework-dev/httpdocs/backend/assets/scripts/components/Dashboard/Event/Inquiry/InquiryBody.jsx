import { PropTypes } from 'react';
import EventBody from '../../EventBody';
import DataList from '../../DataList';

const propTypes = {
    listing: PropTypes.object,
    form:    PropTypes.object,
    comment: PropTypes.string
};

const defaultProps  = {
    listing: null,
    form: null,
    comment: '',
};

const InquiryBody = ({listing, form, comment}) => {

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
        if (form.city) {
            formData.push({
                id: 'city',
                title: 'City',
                value: form.city
            });
        }
        if (form.bedrooms) {
            formData.push({
                id: 'bedrooms',
                title: 'Bedrooms',
                value: form.bedrooms
            });
        }
        if (form.bathrooms) {
            formData.push({
                id: 'bathrooms',
                title: 'Bathrooms',
                value: form.bathrooms
            });
        }
        if (form.square_feet) {
            formData.push({
                id: 'square_feet',
                title: 'Square Feet',
                value: form.square_feet
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
        <p>{comment}</p>
    </EventBody>);
};

InquiryBody.propTypes = propTypes;
InquiryBody.defaultProps = defaultProps;

export default InquiryBody;