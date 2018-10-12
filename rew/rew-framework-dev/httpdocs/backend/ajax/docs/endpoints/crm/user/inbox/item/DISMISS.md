# POST crm/user/inbox/{item_id}/dismiss

Dismiss a user's inbox item

## Parameters

| Key | Type | Optional | Description
| - | - | - | -
| mode | (string) [inquiry,message,register,selling,showing] | no | The type of event item being dismissed

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| success | [Success Response Object](../../../../../objects/SUCCESS_RESPONSE.md) | An object containing successful request details
