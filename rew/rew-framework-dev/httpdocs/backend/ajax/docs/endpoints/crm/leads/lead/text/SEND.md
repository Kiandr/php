# POST crm/leads/{lead_id}/text/send

Send a text message to a specific lead

## Parameters

| Key | Type | Optional | Description
| - | - | - | -
| cell_phone_number | (string) | yes [defaults to lead's cell #] | Override's the lead's existing cell phone #
| content | (string) | no | The text message body

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| success | [Success Response Object](../../../../../objects/SUCCESS_RESPONSE.md) | An object containing successful request details
