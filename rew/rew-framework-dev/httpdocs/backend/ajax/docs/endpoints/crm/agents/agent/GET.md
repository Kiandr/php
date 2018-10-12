# GET crm/agents/{agent_id}

Request a specific agent's details, including CRM authuser data.

## Paramaters

| Key | Type | Optional | Description
| - | - | - | -
| fields | (array) | yes | Limit the data fields that are returned in the result

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| add_sig | (bool) | Flag to include the agent's signature in outgoing CRM emails
| admin | (bool) | Flags whether the agent is an admin
| agent_id | (int) | The agent's CRM ID #
| ar_active | (bool) | Flags whether the agent's auto responder is enabled
| ar_bcc_email | (string) | Agent auto responder BCC email
| ar_cc_email | (string) | Agent auto responder CC email
| ar_document | (string) | Agent auto responder attached document
| ar_is_html | (bool) | Agent auto responder setting: HTML vs Plaintext
| ar_subject | (string) | Agent auto responder subject line
| ar_tempid | (int) | Agent auto responder template ID
| auth | (int) | Agent's CRM authentication ID #
| auto_assign_admin | (bool) | Flags whether the admin allows this agent to receive auto-assigned leads
| auto_assign_agent | (bool) | Flags whether the agent wants to opt-in to receiving auto-assigned leads
| auto_assign_app_id | (int) | Determines which source(s) the agent will accept auto-assigned leads from
| auto_assign_time | (timestamp) | Determines the agent's timeframe for accepting auto-assigned leads
| auto_optout | (bool) | Flags whether agent was automatically opted out
| auto_optout_time | (timestamp) | The timestamp the agent was automatically opted out
| auto_rotate | (bool) | Flags whether the agent is participating in auto-rotated leads
| auto_rotate_app_id | (int) | Determines which source(s) the agent will receive auto-rotated leads from
| auto_search | (bool) | Flags whether suggested searches will be generated for the agent's leads
| blog | (bool) | Flags whether the agent has access to a blog profile
| blog_picture | (string) | The agent's blog image
| blog_profile | (string) | The agent's blog profile details
| blog_signature | (string) | The agent's blog post signature
| blog_signature_on | (bool) | Flags whether the agent's blog post signature will be appended to posts
| cell_phone | (string) | The agent's cell phone #
| cms | (bool) | Flags whether the agent has access to CMS listing manager
| cms_idxs | (string) | Comma seperated list of agent's IDX feeds
| cms_link | (string) | Link to agent's listings
| default_filter | (string) | The agent's default lead results filter
| default_order | (string) | The agent's default lead results search order
| default_sort | (string) | The agent's default lead results search sort order
| display | (bool) | Flags whether the agent will display in the agent roster
| display_feature | (bool) | Flags whether the agent will be featured on the website
| email | (string) | The agent's email address
| fax | (string) | The agent's fax #
| first_name | (string) | The agent's first name
| google_calendar_sync | (bool) | Flags whether the agent's Google calendar will sync with their CRM calendar
| home_phone | (string) | The agent's home phone #
| id | (int) | The agent's CRM ID #
| image | (string) | The agent's photo/image
| last_logon | (timestamp) | The agent's last login timestamp
| last_name | (string) | The agent's last name
| microsoft_calendar_sync | (bool) | Flags whether the agent's Microsoft calendar will sync with their CRM calendar
| notifications | (string) | Json encoded array of agent's notification settings
| office | (int) | ID of the agent's associated office in the CRM
| office_phone | (string) | The phone number of the agent's office
| page_limit | (int) | Default page limit for CRM lead search results
| remarks | (string) | Admin remarks about the agent
| remax_launchpad_url | (string) | URL used for SSO authentication from RE/MAX's Launchpad platform
| remax_launchpad_username | (string) | Username used for SSO authentication from RE/MAX's Launchpad platform
| showing_suite_email | (string) | Email address used to associate the agent with a Showing Suite account 
| signature | (string) | The agent's CRM email signature
| sms_email | (string) | The agent's SMS email address
| timestamp | (timestamp) | The timestamp of the agent's creation in the CRM
| timestamp_created | (timesatmp) | The timestamp of the agent's authuser record creation
| timestamp_reset | (timestamp) | The timestamp of the agent's last password reset
| timestamp_updated | (timestamp) | The timestamp of the last time the agent's profile was edited
| timezone | (string) | The agent's default timezone
| title | (string) | The agent's professional title
| type | (string) | The agent's CRM user type
| website | (string) | The agent's personal website URL
