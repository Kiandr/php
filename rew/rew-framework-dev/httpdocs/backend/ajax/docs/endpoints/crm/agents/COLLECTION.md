# GET crm/leads

Search all available agents.

## Paramaters

| Key | Type | Optional | Description
| - | - | - | -
| limit | (int) | yes | Limit the # of results to pull in this request
| page | (int) | yes | Pagination result offset
| pid | (string) | yes | Enables endpoint result caching. The value is used as part of the memcache key
| search_name | (string) | yes | Search for agents by name (first/last concatinated)

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| agents | array of [Agent Objects](../../../objects/AGENT.md) | An object containing an agent's information
| count | (int) | Total number of results, without considering limits or pagination
| limit | (int) | The active result limiter
| page | (int) | The current pagination position
