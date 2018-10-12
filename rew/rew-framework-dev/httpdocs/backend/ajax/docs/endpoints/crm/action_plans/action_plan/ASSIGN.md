# POST crm/action_plans/action_plan/{action_id}/assign

Assign leads to an action plan.

## Parameters

| Key | Type | Optional | Description
| - | - | - | -
| lead_ids | (array of ints) | no | The ID #s of the leads the agent is being assigned to


## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| success | [Success Response Object](../../../../../objects/SUCCESS_RESPONSE.md) | An object containing successful request details
