# POST crm/leads/{lead_id}/listing/recommend

Recommend a listing to a lead.

## Parameters

| Key | Type | Optional | Description
| - | - | - | -
| message | (string) | yes | The email message/body if notify is enabled
| mls_number | (string) | no | The MLS # of the listing that is being recommended
| notify | (bool) | yes | Determines whether a notification email will send to the lead

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| success | [Success Response Object](../../../../../objects/SUCCESS_RESPONSE.md) | An object containing successful request details
