# Lead Object

| Key | Type | Description
| - | - | -
| id | (int) | The lead's ID
| first_name | (string) | The lead's first name
| last_name | (string) | The lead's last name
| email | (string) | The lead's email address
| image | (string/null) | The lead's image/photo
| online | (bool) | Determines whether the lead is currently online
| opt_marketing | (string) | The lead's opt-in status for automated notifications
| opt_searches | (string) | The lead's opt-in status for IDX search notifications
| opt_texts | (string) | The lead's opt-in status for text messaging
| phone | (string/null) | The lead's phone #
| phone_cell | (string/null) | The lead's cell phone #
| keywords | (string/null) | The lead's keywords
| notes | (string/null) | The lead's notes
| score | (int) | The lead's calculated score
| source | (string/null) | The lead's source
| status | (string) | The leads "accepted" status
| timestamp_created | (timestamp) | The timestamp the lead was created
| timestamp_active | (timestamp) | The timestamp the lead was last active
| value | (int) | The lead's value
| num_calls | (int) | The number of calls that have been sent to the lead through the CRM
| num_emails | (int) | The number of emails that have been sent to the lead through the CRM
| num_texts | (int) | The number of texts that have been send to the lead through the CRM
| num_forms | (int) | The number of forms the lead has filled out
| num_messages | (int) | The number of messages the sent to the lead through the CRM
| num_favorites | (int) | The number of listings the lead has favorited
| num_visits | (int) | The number of visits the lead has made to the site
| num_listings | (int) | The number of listings that the lead has viewed
| num_searches | (int) | The number of searches that the lead has made
| last action | Object of [lead.last_action Objects](#last_action) | Object of what and when the lead was last took action on the site
| last touched | Object of [lead.last_touched Objects](#last_touched) | Object of when the lead was last contacted through the CRM and how
| agent | Object of [lead.agent Object](#lead_agent) | Agent assigned to the lead
| lender | Object of [lead.lender Object](#lead_lender) | Lender assigned to the lead
| groups | Array of [lead.groups Objects](#lead_groups) | List of groups the lead is assigned to
| action plans | Array of [lead.action_plans Objects](#lead_action_plans) | List of action plans assigned to the lead

### <a id="lead_last_action">lead.last_action</a>

| Key | Type | Description
| - | - | -
| title | (string/null) | The title of how the lead last took action on the site
| timestamp | (timestamp) | The timestamp from when the lead was last texted/called/emailed form the CRM
| url | (string/null) | The url where the lead last took action on the site


### <a id="lead_last_touched">lead.last_touched</a>

| Key | Type | Description
| - | - | -
| timestamp | (timestamp) | The timestamp from when the lead was last texted/called/emailed form the CRM
| type | (string) | The timestamp from when the lead was last texted/called/emailed form the CRM
| method | (string) | The method in which the lead was last contacted through the CRM

### <a id="lead_agent">lead.agent</a>

| Key | Type | Description
| - | - | -
| agent_id | (int) | The lead's assigned agent's ID
| first_name | (string) | The lead's assigned agent's first name
| last_name | (string) | The lead's assigned agent's last name
| image | (string/null) | The agents's image/photo

### <a id="lead_lender">lead.lender</a>

| Key | Type | Description
| - | - | -
| id | (int) | The lead's assigned lender's ID
| first_name | (string/null) | The lead's assigned lender's first name
| last_name | (string/null) | The lead's assigned lender's last name
| image | (string/null) | The lender's image/photo

### <a id="lead_groups">lead.groups</a>

| Key | Type | Description
| - | - | -
| agent_id | (int/null) | The agent the group belongs to
| associate_id | (int/null) | The ISA the group belongs to
| description | (string/null) | The group description
| id | (int/null) | The group ID
| is_shared | (bool) | Determines whether a group is shared with all users
| name | (string) | The name of the group
| style | (string) | An identifier used to associate the group with a colour
| timestamp_created | (timestamp) | The timestamp from when the group was created

### <a id="action_plans">lead.action_plans</a>

| Key | Type | Description
| - | - | -
| id | (int/null) | The agent the group belongs to
| name | (string) | The name of the Action Plan
| description | (string/null) | The action plan description
| day_adjust | (string/null) | day adjustment of the action plan tasks
| style | (string) | An identifier used to associate the action plan with a colour
| timestamp_created | (timestamp) | The timestamp from when the action plan was created
| timestamp_updated | (timestamp) | The timestamp from when the action plan was updated
