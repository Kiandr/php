# Action Plan Object

| Key | Type | Description
| - | - | -
| count_tasks | (int) | The number of tasks associated with the action plan
| day_adjust | (string) | A comma seperated list of days the action plan will be active
| description | (string) | The action plan's description
| id | (int) | The action plan's ID #
| name | (string) | The name/title of the action plan
| style | (string) | An identifier used to determine which colour is associated with the action plan in the UX
| tasks | Array of [action_plan.tasks Objects](#action_plan_tasks) | List of tasks associated with the action plan
| timestamp_created | (timestamp) | The timestamp from when the action plan was created
| timestamp_updated | (timestamp) | The timestamp from when the action plan was last updated

### <a id="action_plan_tasks">action_plan.tasks</a>

| Key | Type | Description
| - | - | -
| automated | (bool) | Flags whether the task will be resolved automatically by the CRM
| due_after_days | (int) | # of days after assignment until the task is due
| due_time | (string) [HH:MM:SS] | Time of day the plan will be flagged as due
| expire_after_days | (int) | # of days after being due until the task is expired
| id | (int) | The task's ID #
| info | (string) | Additional information about the task
| name | (string) | The name/title of the task
| parent_task_id | (int|null) | If this is a "follow-up" task, this will contain the ID # of the task's parent task
| performer | (string) | The user that will be required to complete the task
| type | (string) | The type of task
| timestamp_created | (timestamp) | The timestamp from when the task was created
| timestamp_updated | (timestamp) | The timestamp from when the task was last updated