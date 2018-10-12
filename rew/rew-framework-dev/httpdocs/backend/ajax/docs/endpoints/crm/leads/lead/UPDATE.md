# PUT crm/leads

Update a specific lead's information.

## Paramaters

| Key | Type | Optional | Description
| - | - | - | -
| address1 | (string) | yes | The lead's address line 1
| address2 | (string) | yes | The lead's address line 2
| address3 | (string) | yes | The lead's address line 3
| city | (string) | yes | The lead's city
| comments | (string) | yes | Comments about the lead
| contact_method | (string) [email,phone,text] | yes | The lead's preferred method of contact
| country | (string) | yes | The lead's country
| email | (string) | yes |  The lead's email address
| email_alt | (string) | yes | The lead's alternate email address
| first_name | (string) | yes | The lead's first name
| in_shark_tank | (bool) | yes | Flags whether the lead will appear in the shark tank page
| keywords | (string) | yes | The lead's SEO keywords
| last_name | (string) | yes | The lead's last name
| notes | (string) | yes | The lead's quick note
| notify_favs | (bool) | yes | Flags whether the assigned agent will be notified when the lead saves a favourite IDX listing
| notify_searches | (bool) | yes | Flags whether the assigned agent will be notified when the lead saves an IDX search
| origin | (string) | yes | The lead's origin/source
| phone | (string) | yes | The lead's primary phone #
| phone_cell | (string) | yes | The lead's cell phone #
| phone_cell_status | (int) [1-7] | yes | The status of the lead's cell phone #
| phone_home_status | (int) [1-7] | yes | The status of the lead's home phone #
| phone_fax | (string) | yes | The lead's fax #
| phone_work | (string) | yes | The lead's work phone #
| phone_work_status | (int) [1-7] | yes | The status of the lead's work phone #
| remarks | (string) | yes | Remarks about the lead
| search_city | (string) | yes | The lead's calculated search city
| search_maximum_price | (string) | yes | The lead's calculated minimum price
| search_minimum_price | (string) | yes | The lead's calculated maximum price
| search_subdivision | (string) | yes | The lead's calculated subdivision
| search_type | (string) | yes | The lead's calculated property type
| share_lead | (bool) | yes | Flags whether the lead is shared with the assigned agent's team
| state | (string) | yes | The lead's state
| zip | (string) | yes | The lead's zip code

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| success | [Success Response Object](../../../../objects/SUCCESS_RESPONSE.md) | An object containing successful request details
