# POST crm/leads/{lead_id}/groups/assign

Assign a lead to CRM group(s).

## Parameters

| Key | Type | Optional | Description
| - | - | - | -
| group_ids | (array of ints) | no | The ID #s of the groups the lead is being assigned to

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| success | [Success Response Object](../../../../../objects/SUCCESS_RESPONSE.md) | An object containing successful request details
