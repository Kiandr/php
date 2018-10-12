# GET crm/leads

Search all available CRM groups.

## Paramaters

| Key | Type | Optional | Description
| - | - | - | -
| limit | (int) | yes | Limit the # of results to pull in this request
| page | (int) | yes | Pagination result offset
| type | (string) | yes | Limits the type of group being requested (agent, campaign, lead)
| type_id | (int) | yes | The ID # of the type of group being requested

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| count | (int) | Total number of results, without considering limits or pagination
| leads | array of [Group Objects](../../../objects/GROUP.md) | An object containing a group's information
| limit | (int) | The active result limiter
| page | (int) | The current pagination position
