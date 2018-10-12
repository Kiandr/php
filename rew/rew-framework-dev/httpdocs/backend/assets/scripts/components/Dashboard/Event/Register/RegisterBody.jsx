import { PropTypes } from 'react';
import EventBody from '../../EventBody';
import DataList from '../../DataList';

const propTypes = {
    lead: PropTypes.object.isRequired
};

const RegisterBody = ({lead}) => {

    let registerData = [];
    if (lead){
        if (lead.name) {
            registerData.push({
                id: 'name',
                title: 'Name',
                value: lead.name
            });
        }
        if (lead.email) {
            registerData.push({
                id: 'email',
                title: 'Email',
                value: lead.email,
                link: lead.emailLink,
            });
        }
        if (lead.phone) {
            registerData.push({
                id: 'phone',
                title: 'Phone',
                value: lead.phone,
                link: lead.phoneLink
            });
        }
    }

    let detailsTable = '';
    if (registerData.length > 0) {
        detailsTable = <DataList
            data={registerData}
        />;
    }

    // Build Body Element
    return (<EventBody>
        {detailsTable}
    </EventBody>);
};

RegisterBody.propTypes = propTypes;

export default RegisterBody;