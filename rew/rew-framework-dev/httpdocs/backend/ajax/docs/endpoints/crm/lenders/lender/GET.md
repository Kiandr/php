# GET crm/lenders/{lender_id}

Request a specific lender's details, including CRM authuser data.

## Paramaters

| Key | Type | Optional | Description
| - | - | - | -
| fields | (array) | yes | Limit the data fields that are returned in the result

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| address | (string) | The lender's address
| auth | (int) | The lender CMR authuser ID #
| auto_assign_admin | (bool) | Determines whether the admin allows leads to be auto-assigned to this lender
| auto_assign_optin | (bool) | Determines whether the lender will accept auto-assigned leads
| auto_assign_time | (timestamp) | Tracks the last timestamp that a lead was auto-assigned to this lender
| cell_phone | (string) | The lender's cell phone #
| city | (string) | The lender's city
| default_filter | (string) | The lender's default search filter for the main CRM leads page
| default_order | (string) | The lender's default search order for the main CRM leads page
| default_sort | (string) | The lender's default search sort order for the main CRM leads page
| email | (string) | The lender's email address
| fax | (string) | The lender's fax number
| first_name | (string) | The lender's first name
| home_phone | (string) | The lender's home phone #
| id | (int) | The lender's CRM ID #
| last_logon | (timestamp) | The lenders last login timestamp
| last_name | (string) | The lender's last name
| office_phone | (string) | The lender's office phone #
| page_limit | (int) | The lender's default search result limit for the main CRM leads page
| state | (string) | The lender's state
| timestamp_created | (timestamp) | The timestamp that the lender's profile was created
| timestamp_reset | (timestamp) | The timestamp of the last time the lender reset their password
| timestamp_updated | (timestamp) | The timestamp of the last time the lender's profile was updated
| timezone | (int) | The lender's preferred timezone
| type | (string) | The lender's authuser account type
| zip | (string) | The lender's zip code
