# POST crm/groups/assign

Assign leads to groups.

## Parameters

| Key | Type | Optional | Description
| - | - | - | -
| lead_ids | (array of ints) | no | The ID #s of the leads being assigned to the groups
| group_ids | (array of ints) | no | The ID #s of the groups the leads are being assigned to


## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| success | [Success Response Object](../../../../../objects/SUCCESS_RESPONSE.md) | An object containing successful request details
