# GET crm/leads/{lead_id}/listings

Get the lead's latest listing information: 
* Favorite Listings
* Inquiries
* Recommended Listings
* Showing Requests
* Viewed Listings

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| favorites | [Lead Listing Object](../../../../../objects/LEAD_LISTING.md) | Contains the total favorite count + basic MLS data from the last listing favorited
| inquiries | [Lead Listing Object](../../../../../objects/LEAD_LISTING.md) | Contains the total inquiry count + basic MLS data from the last inquiry
| recommended | [Lead Listing Object](../../../../../objects/LEAD_LISTING.md) | Contains the total recommended listing count + basic MLS data from the last recommended listing
| showings | [Lead Listing Object](../../../../../objects/LEAD_LISTING.md) | Contains the total showing request count + basic MLS data from the last showing request
| views | [Lead Listing Object](../../../../../objects/LEAD_LISTING.md) | Contains the total view count + basic MLS data from the last listing viewed
