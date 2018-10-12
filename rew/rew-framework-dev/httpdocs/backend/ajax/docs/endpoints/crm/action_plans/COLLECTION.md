# GET crm/leads

Search all available action plans.

## Paramaters

| Key | Type | Optional | Description
| - | - | - | -
| hide_tasks | (bool) | yes | Prevents the action plan tasks from being included in the response package

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| action_plans | array of [Action Plan Objects](../../../objects/ACTION_PLAN.md) | An object containing an action plan's information
| count_total_action_plans | (int) | Total number of action plans
| count_total_tasks | (int) | Total number of combined action plan tasks
