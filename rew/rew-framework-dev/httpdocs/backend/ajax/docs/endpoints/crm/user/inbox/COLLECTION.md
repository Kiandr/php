# GET crm/user/inbox

Search all available user inbox items.

## Paramaters

| Key | Type | Optional | Description
| - | - | - | -
| limit | (int) | yes | Limit the # of results to pull in this request
| mode | (string) [inquiry,message,register,selling,showing] | yes | Filter the results by inbox event type

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| agent_id | (int) | The user's CRM agent ID #
| count | (int) | Total number of results, without considering limits or pagination
| events | array of [Inbox Item Objects](../../../../objects/INBOX_ITEM.md) | An object containing an inbox item's information
| limit | (int) | The active result limiter
