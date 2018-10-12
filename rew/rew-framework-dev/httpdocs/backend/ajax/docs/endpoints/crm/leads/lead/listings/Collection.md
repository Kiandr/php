# GET crm/leads/{lead_id}/listings/{type}

Get one of the three types of the lead's latest listing information: 
* Favorite Listings
* Recommended Listings
* Viewed Listings

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| favorites |  Contains the listing data for the leads favorited listings
| views |  Contains the listing data for the leads viewed listings
| recommended |  Contains tge listing data for the leads recommended listings