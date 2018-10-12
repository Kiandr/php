import { PropTypes } from 'react';
import SellingHeader from './Selling/SellingHeader';
import SellingBody from './Selling/SellingBody';
import SellingFooter from './Selling/SellingFooter';

const propTypes = {
    hash: PropTypes.string.isRequired,
    data: PropTypes.object.isRequired,
    timestamp: PropTypes.number.isRequired,
    hideEvent: PropTypes.func.isRequired
};

const defaultProps = {
    apiKey: null
};

const Selling = ({ hash, data, timestamp, hideEvent }) => {

    const report = data.form.type === 'CMA Form' ? 'Comparative Market Analysis' : 'Property Valuation Request';
    
    // Build Event
    return <div className='mda'>
        <SellingHeader
            lead={data.lead}
            report={report}
            form_id={parseInt(data.form.id)}
            timestamp={timestamp}
            hash={hash}
            hideEvent={hideEvent}
        />
        <SellingBody
            map={data.map}
            address={data.form.address}
            bedrooms={data.form.bedrooms}
            bathrooms={data.form.bathrooms}
            square_feet={data.form.square_feet}
            price_range={data.form.price_range}
            move_when={data.form.move_when}
            comments={data.form.comments}
        />
        <SellingFooter
            hash={hash}
            lead={data.lead}
            form={data.form}
            hideEvent={hideEvent}
        />
    </div>;
};

Selling.propTypes = propTypes;
Selling.defaultProps = defaultProps;

export default Selling;