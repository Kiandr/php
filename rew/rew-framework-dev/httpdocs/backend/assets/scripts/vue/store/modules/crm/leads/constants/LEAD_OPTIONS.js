const YES_NO_OPTIONS = [
    { value: 'yes', text: 'Yes' },
    { value: 'no', text: 'No' }
];

const IN_OUT_OPTIONS = [
    { value: 'in', text: 'Yes' },
    { value: 'out', text: 'No' }
];

const BOOLEAN_OPTIONS = [
    { value: 'true', text: 'Yes' },
    { value: 'false', text: 'No' }
];

export default {
    groups: [],
    agents: [],
    lenders: [],
    action_plans: [],
    status: [
        { value: 'pending', text: 'Pending' },
        { value: 'accepted', text: 'Accepted' },
        { value: 'rejected', text: 'Rejected' },
        { value: 'closed', text: 'Closed' }
    ],
    heat: [
        { value: 'hot', text: 'Hot' },
        { value: 'mediumhot', text: 'Medium Hot' },
        { value: 'warm', text: 'Warm' },
        { value: 'lukewarm', text: 'Luke Warm' },
        { value: 'cold', text: 'Cold' }
    ],
    social: [
        { value: 'facebook', text: 'Facebook' },
        { value: 'microsoft', text: 'Windows Live' },
        { value: 'google', text: 'Google' },
        { value: 'linkedin', text: 'LinkedIn' },
        { value: 'twitter', text: 'Twitter' },
        { value: 'yahoo', text: 'Yahoo!' }
    ],
    order: [
        { value: 'score', text: 'Score' },
        { value: 'value', text: 'Value' },
        { value: 'last_name,first_name', text: 'Name' },
        { value: 'email', text: 'Email' },
        { value: 'status', text: 'Status' },
        { value: 'num_visits', text: '# of Visits' },
        { value: 'num_forms', text: 'Forms' },
        { value: 'num_emails', text: '# of Emails' },
        { value: 'num_calls', text: '# of Calls' },
        { value: 'num_texts', text: '# of Text' },
        { value: 'num_listings', text: '# of Listings' },
        { value: 'num_favorites', text: '# of Favorites' },
        { value: 'num_searches', text: '# of Searches' },
        { value: 'agent', text: 'Agent' },
        { value: 'lender', text: 'Lender' },
        { value: 'timestamp_created', text: 'Date/Time Created' },
        { value: 'timestamp_active', text: 'Last Active' },
        { value: 'last_touched', text: 'Last Touched' }
    ],
    sort: [
        { value: 'ASC', text: 'Asc' },
        { value: 'DESC', text: 'Desc' }
    ],
    action_plan_status: [
        { value: 'completed', text: 'Completed' },
        { value: 'progress', text: 'In Progress' }
    ],
    action_plan_due_tasks: BOOLEAN_OPTIONS,
    action_plan_types: [
        { value: 'Call', text: 'Call' },
        { value: 'Custom', text: 'Custom' },
        { value: 'Email', text: 'Email' },
        { value: 'Group', text: 'Group' },
        { value: 'Listing', text: 'Listing' },
        { value: 'Search', text: 'Search' }
    ],
    opt_marketing: IN_OUT_OPTIONS,
    opt_searches: IN_OUT_OPTIONS,
    verified: YES_NO_OPTIONS.concat([
        { value: 'pending', text: 'Pending' }
    ]),
    has_phone: YES_NO_OPTIONS,
    opt_texts: IN_OUT_OPTIONS,
    contact_method: [
        { value: 'email', text: 'Email' },
        { value: 'phone', text: 'Phone' },
        { value: 'text', text: 'Text' }
    ],
    bounced: YES_NO_OPTIONS,
    fbl: YES_NO_OPTIONS
};
