# GET crm/leads

Search all available leads.

## Paramaters

| Key | Type | Optional | Description
| - | - | - | -
| active_end | (string) [YYYY-MM-DD] | yes | Search by lead's maximum active date
| active_start | (string) [YYYY-MM-DD] | yes | Search by lead's minimum active date
| agents | (array of ints) | yes | Search by lead's assigned agent(s)
| bounced | (string) [yes,no] | yes | Search for leads by bounced email status
| calls_max | (int) | yes | Search range max: # of logged phone calls
| calls_min | (int) | yes | Search range min: # of logged phone calls
| contact_method | (string) [email,phone,text] | yes | Search by lead's preferred contact method
| date_end | (string) [YYYY-MM-DD] | yes | Search by lead's maximum creation date
| date_start | (string) [YYYY-MM-DD] | yes | Search by lead's minimum creation date
| email | (string) | yes | Search by lead's email address
| emails_max | (int) | yes | Search range max: # of logged emails
| emails_min | (int) | yes | Search range min: # of logged emails
| favorites_max | (int) | yes | Search range max: # of favourited IDX listings
| favorites_min | (int) | yes | Search range min: # of favourited IDX listings
| fbl | (string) [yes,no] | yes | Search for leads by email FBL status
| first_name | (string) | yes | Search by lead's first name
| groups | (array of ints) | yes | Search for lead's by assigned CRM group(s)
| has_phone | (string) [yes,no] | yes | Search for leads with at least one phone number
| heat | (string) | yes | Search by lead's heat
| inquiries_max | (int) | yes | Search range max: # of IDX listing inquiries
| inquiries_min | (int) | yes | Search range min: # of IDX listing inquiries
| last_name | (string) | yes | Search by lead's last name
| lenders | (array of ints) | yes | Search by lead's assigned lender(s)
| limit | (int) | yes | Limit the # of results to pull in this request
| listings_max | (int) | yes | Search range max: # of viewed IDX listings
| listings_min | (int) | yes | Search range min: # of viewed IDX listings
| opt_marketing | (string) [in,out] | yes | Search by lead's automated email opt-in status
| opt_searches | (string) [in,out] | yes | Search by lead's saved search email update opt-in status
| opt_texts | (string) [in,out] | yes | Search by lead's text message opt-in status
| order | (string) [active,created,email,name,score,status,value] | yes | Search results column order
| page | (int) | yes | Pagination result offset
| phone | (string) | yes | Search by lead's primary phone number
| pid | (string) | yes | Enables endpoint result caching. The value is used as part of the memcache key
| search_city | (string) | yes | Search by lead's calculated city
| search_ip | (string) | yes | Search by lead's IP address
| search_maximum_price | (int) | yes | Search by lead's calculated maximum price
| search_minimum_price | (int) | yes | Search by lead's calculated minimum price
| search_referer | (string) | yes | Search for leads by referer
| search_subdivision | (string) | yes | Search by lead's calculated subdivision
| search_type | (string) | yes | Search by lead's calculated primary property type
| searches_max | (int) | yes | Search range max: # of saved IDX searches
| searches_min | (int) | yes | Search range min: # of saved IDX searches
| social | (array of strings) | yes | Search leads by social network
| sort | (string) [ASC,DESC] | yes | Search results sort order
| status | (string &#124; array of strings) | yes | Search by lead's status
| teams | (array of ints) | yes | Search by lead's assigned team(s)
| texts_incoming_max | (int) | yes | Search range max: # of logged incoming text messages
| texts_incoming_min | (int) | yes | Search range min: # of logged incoming text messages
| texts_outgoing_max | (int) | yes | Search range max: # of logged outgoing text messages
| texts_outgoing_min | (int) | yes | Search range min: # of logged outgoing text messages
| verified | (string) [yes,no,pending] | yes | Search leads by email verification status
| view | (string) [accepted,all-leads,inquiries,my-leads,online,pending,rejected,unassigned] | yes | Allows you to select from a set of pre-defined filter options
| visits_max | (int) | yes | Search range max: # of visits
| visits_min | (int) | yes | Search range min: # of visits

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| count | (int) | Total number of results, without considering limits or pagination
| leads | array of [Lead Objects](../../../objects/LEAD.md) | An object containing a lead's information
| limit | (int) | The active result limiter
| page | (int) | The current pagination position
