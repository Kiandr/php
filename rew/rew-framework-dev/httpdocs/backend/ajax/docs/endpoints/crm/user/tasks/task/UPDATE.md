# PUT crm/user/tasks/{user_task_id}

Update a specific assigned task's details.

## Parameters

| Key | Type | Optional | Description
| - | - | - | -
| note | (string) | yes | The note that will be appended to the assigned task for this action/update
| duration | (int) | [required if action=snooze] | The amount of time to be utilized with specific actions
| unit | (string) [minutes, hours, days, weeks] | [required if action=snooze] | The unit of time used to calculate the action duration
| dismiss_followup_tasks | (bool) | yes | Flags the request to also dismiss any follow up tasks for the target assigned task
| action | (string) [complete, dismiss, note, snooze] | no | The action being performed on the assigned task

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| success | [Success Response Object](../../../../../objects/SUCCESS_RESPONSE.md) | An object containing successful request details
