# POST crm/leads/{lead_id}/email/send

Send an email to a lead.

## Parameters

| Key | Type | Optional | Description
| - | - | - | -
| bccs | (array of strings) | yes | List of addresses to BCC on the email
| ccs | (array of strings) | yes | List of addresses to CC on the email
| content | (string) | no | The content body of the email
| subject | (string) | no | The subject line for the email

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| success | [Success Response Object](../../../../../objects/SUCCESS_RESPONSE.md) | An object containing successful request details
