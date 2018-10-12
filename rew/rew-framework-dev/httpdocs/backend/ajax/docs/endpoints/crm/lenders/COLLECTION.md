# GET crm/lenders

Search all available lenders.

## Paramaters

| Key | Type | Optional | Description
| - | - | - | -
| limit | (int) | yes | Limits the maximum number of results in the response
| page | (int) | yes | Pagination result offset
| pid | (string) | yes | Enables endpoint result caching. The string will be included in the memcache key.
| search_name | (string) | yes | Search by lender name (first/last concatinated)

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| count | (int) | Total number of results, without considering limits or pagination
| lenders | array of [Lender Objects](../../../objects/LENDER.md) | An object containing a lender's information
| limit | (int) | The active result limiter
| page | (int) | The current pagination position
