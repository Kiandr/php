# GET crm/user

Request the active authuser's information

## Paramaters

| Key | Type | Optional | Description
| - | - | - | -
| fields | (array) | yes | Limit the data fields that are returned in the result

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| TZ | (string) | The user's preferred timezone
| admin | (bool) | Determines whether the user has admin permissions
| email | (string) | The user's email address
| first_name | (string) | The user's first name
| id | (string) | The user's ID
| image | (string) | The user's image/photo URL
| last_name | (string) | The user's last name
| permissions | (array) | Assoc array of user's CRM permissions
| text_available | (bool) | 
| type | (string) [Agent &#124; Associate &#124; Lender] | The user's CRM account type
