# POST crm/leads/{lead_id}/note/add

Add a CRM note to a lead.

## Parameters

| Key | Type | Optional | Description
| - | - | - | -
| content | (string) | no | The content body of the note
| share | (bool) | yes | Determines whether this note will be shared with (IE: visible to) other CRM agents

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| success | [Success Response Object](../../../../../objects/SUCCESS_RESPONSE.md) | An object containing successful request details
