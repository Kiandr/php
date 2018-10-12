import { createElement } from 'react';
import { render } from 'react-dom';
import DashboardContainer from '../../components/Dashboard/DashboardContainer';

// Get event data from html element
var eventData = $('div#report__data');

// Get event components
var events = eventData.data('events');
var timestamp = parseInt(eventData.data('timestamp'));
var eventKey = eventData.data('api-key');

// Get Data to Load at End of Page
var nextUnloadedMessage = eventData.data('next-unloaded-message');
var nextUnloadedRegister = eventData.data('next-unloaded-register');
var nextUnloadedInquiry = eventData.data('next-unloaded-inquiry');
var nextUnloadedSelling = eventData.data('next-unloaded-selling');
var nextUnloadedShowing = eventData.data('next-unloaded-showing');

//Get Unloaded Event Counts
var unloadedMessageCount = eventData.data('unloaded-message-count');
var unloadedRegisterCount = eventData.data('unloaded-register-count');
var unloadedInquiryCount = eventData.data('unloaded-inquiry-count');
var unloadedShowingCount = eventData.data('unloaded-showing-count');
var unloadedSellingCount = eventData.data('unloaded-selling-count');

var authId = eventData.data('auth-id');

//Get Dashboard Container
var dashboard = document.getElementById('report');

// Load Google Maps
$.ajax({
    url: '//maps.googleapis.com/maps/api/js?v=3&libraries=geometry&key=' + eventKey,
    dataType: 'script'
}).done(() => {

    // Get Dashboard Element
    var dashboardElement = createElement(
        DashboardContainer, 
        {
            events: events || [],
            timestamp: timestamp || null,
            nextUnloadedMessage: nextUnloadedMessage || null,
            nextUnloadedRegister: nextUnloadedRegister || null,
            nextUnloadedInquiry: nextUnloadedInquiry || null,
            nextUnloadedSelling: nextUnloadedSelling || null,
            nextUnloadedShowing: nextUnloadedShowing || null,
            unloadedMessageCount: unloadedMessageCount || 0,
            unloadedRegisterCount: unloadedRegisterCount || 0,
            unloadedInquiryCount: unloadedInquiryCount || 0,
            unloadedShowingCount: unloadedShowingCount || 0,
            unloadedSellingCount: unloadedSellingCount || 0,
            authId: authId
        }
    );

    //Render Root Component
    render(
        dashboardElement, 
        dashboard
    );
});