# GET crm/user/tasks

Search a user's assigned tasks.

## Paramaters

| Key | Type | Optional | Description
| - | - | - | -
| hide_automated | (bool) | yes | Determines whether automated tasks will be included in the results
| limit | (int) | yes | Limit the # of results to pull in this request
| max_timestamp_due | (int) | yes | Search by maximum timestamp due
| min_timestamp_due | (int) | yes | Search by minimum timestamp due
| max_timestamp_expire | (int) | yes | Search by maximum timestamp expired
| min_timestamp_expire | (int) | yes | Search by minimum timestamp expired
| max_timestamp_resolved | (int) | yes | Search by maximum timestamp resolved
| min_timestamp_resolved | (int) | yes | Search by minimum timestamp resolved
| max_timestamp_scheduled | (int) | yes | Search by maximum timestamp scheduled
| min_timestamp_scheduled | (int) | yes | Search by minimum timestamp scheduled
| page | (int) | yes | Pagination result offset
| statuses | (array of strings) | yes | Search by task status(es)

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| count | (int) | Total number of results, without considering limits or pagination
| limit | (int) | The active result limiter
| page | (int) | The current pagination position
| tasks | array of [User Task Objects](../../../../objects/USER_TASK.md) | An object containing a user task's information
