import { PureComponent, PropTypes } from 'react';
import EventBody from '../../EventBody';
import DataList from '../../DataList';

const propTypes = {
    map: PropTypes.object,
    address: PropTypes.string,
    bedrooms: PropTypes.string,
    bathrooms: PropTypes.string,
    square_feet: PropTypes.string,
    price_range: PropTypes.string,
    move_when: PropTypes.string,
    comments: PropTypes.string
};

const defaultProps = {
    map: null,
    address: '-',
    bedrooms: '-',
    bathrooms: '-',
    square_feet: '-',
    price_range: '-',
    move_when: '-',
    comments: ''
};

class SellingBody extends PureComponent {

    componentDidMount() {

        if (this.props.map && window.google) {

            const lat = this.props.map.lat;
            const lng = this.props.map.lng;
            
            const directionsService =    new window.google.maps.DirectionsService();
            const directionRequest = {
                origin:      lat + ', ' + lng, 
                destination: lat + ', ' + lng,
                travelMode:  window.google.maps.DirectionsTravelMode.DRIVING
            };
            directionsService.route(directionRequest, $.proxy(function (response, status) {

                // Check for success
                if (status == window.google.maps.DirectionsStatus.OK) {

                    // Step Back from AddressLocation to get Camera Location
                    const cameraLocation = response.routes[0].legs[0].steps[0].start_location;

                    // Center Point
                    const addressLocation = new window.google.maps.LatLng(lat,lng);
                    
                    // Calculate Heading for POV
                    const heading = window.google.maps.geometry.spherical.computeHeading(cameraLocation, addressLocation);

                    // Setup Panorama
                    new window.google.maps.StreetViewPanorama(this.streetImage, {
                        position : addressLocation,
                        pov : {
                            heading : heading,
                            pitch : 0,
                            zoom : 1
                        },
                        disableDefaultUI: true
                    });
                }
            }, this));
        }
    }

    render() {

        let streetview;
        if (this.props.map) {
            streetview = (<div
                id='streetview-container'
                ref={(div) => { this.streetImage = div; }}
            />);
        }

        let formData = [];
        if (this.props.address) {
            formData.push({
                id: 'address',
                title: 'Address',
                value: this.props.address
            });
        }
        if (this.props.bedrooms) {
            formData.push({
                id: 'bedrooms',
                title: 'Bedrooms',
                value: this.props.bedrooms
            });
        }
        if (this.props.bathrooms) {
            formData.push({
                id: 'bathrooms',
                title: 'Bathrooms',
                value: this.props.bathrooms
            });
        }
        if (this.props.square_feet) {
            formData.push({
                id: 'square_feet',
                title: 'Square Feet',
                value: this.props.square_feet
            });
        }
        if (this.props.price_range) {
            formData.push({
                id: 'price_range',
                title: 'Price Range',
                value: this.props.price_range
            });
        }
        if (this.props.move_when) {
            formData.push({
                id: 'move_when',
                title: 'Timeframe',
                value: this.props.move_when
            });
        }

        let formTable = '';
        if (formData.length > 0) {
            formTable = <DataList
                data={formData}
            />;
        }

        // Build Body Element
        return (<EventBody>
            {streetview}
            {formTable}
            <p>{this.props.comments}</p>
        </EventBody>);
    }
}

SellingBody.propTypes = propTypes;
SellingBody.defaultProps = defaultProps;

export default SellingBody;