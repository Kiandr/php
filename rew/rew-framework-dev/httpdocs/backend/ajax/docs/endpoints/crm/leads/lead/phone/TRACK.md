# POST crm/leads/{lead_id}/phone/track

Log a phone call for a lead

## Parameters

| Key | Type | Optional | Description
| - | - | - | -
| details | (string) | no | The call log details
| type | (string) [attempt,call,invalid,voicemail] | no | The response type of the call attempt

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| success | [Success Response Object](../../../../../objects/SUCCESS_RESPONSE.md) | An object containing successful request details
