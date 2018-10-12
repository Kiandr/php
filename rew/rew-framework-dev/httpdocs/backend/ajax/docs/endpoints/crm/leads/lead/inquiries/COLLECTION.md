# GET crm/leads/{lead_id}/inquiries/{type}

Get one of the three types of the lead's latest Inquiry information: 
* Listing Inquiries
* Showing Requests

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| Listing Inquiries | inquiry | Contains the listing data for the leads Listing Inquiries
| Showing Requests | showing | Contains tge listing data for the leads Showing Requests
