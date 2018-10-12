# Internal API Documentation

The purpose of this API is to allow for the framework to make same-domain asynchronous requests to the server. 

The current use-case is with our CRM Vue.js library; it communicates with the API to perform server-level requests and manipulate the UX based on response codes and data.

## Authentication

This API lives within the framework and only accepts requests from the same domain that hosts it.

Authentication is handled through the framework's Auth class. Requests will be authenticated by checking your active CRM user session.

## Response Codes

```sh
200: Success
400: Bad request
401: Insufficient Permissions
403: Forbidden
404: Not found
500: Server Error
```

## API Endpoints

All endpoints referenced in this documentation start with the base URL:
```sh
http://{current_domain}/backend/ajax/
```

### CRM

#### Action Plans

- [Search available action plans `GET crm/action_plans`](endpoints/crm/action_plans/COLLECTION.md)

#### Agents

- [Search available agents `GET crm/agents`](endpoints/crm/agents/COLLECTION.md)
- [Search an agent `GET crm/agents/{agent_id}`](endpoints/crm/agents/agent/GET.md)

#### Email

- [Validate an email address `GET crm/email/validate`](endpoints/crm/email/VALIDATE.md)

#### Groups

- [Search available groups `GET crm/groups`](endpoints/crm/groups/COLLECTION.md)

#### Leads

- [Search available leads `GET crm/leads`](endpoints/crm/leads/COLLECTION.md)
  - [Update a lead `PUT crm/leads/{lead_id}`](endpoints/crm/leads/lead/UPDATE.md)
  - [Delete a lead `DELETE crm/leads/{lead_id}`](endpoints/crm/leads/lead/DELETE.md)
  - [Assign a lead to an agent `POST crm/leads/{lead_id}/agent/{agent_id}/assign`](endpoints/crm/leads/lead/agent/ASSIGN.md)
  - [Unassign a lead from an agent `POST crm/leads/{lead_id}/agent/{agent_id}/unassign`](endpoints/crm/leads/lead/agent/UNASSIGN.md)
  - [Assign a lead to a lender `POST crm/leads/{lead_id}/lender/{lender_id}/assign`](endpoints/crm/leads/lead/lender/ASSIGN.md)
  - [Unassign a lead from a lender `POST crm/leads/{lead_id}/lender/{lender_id}/assign`](endpoints/crm/leads/lead/lender/UNASSIGN.md)
  - [Assign a lead to group(s) `POST crm/leads/{lead_id}/groups/assign`](endpoints/crm/leads/lead/groups/ASSIGN.md)
  - [Unassign a lead to group(s) `POST crm/leads/{lead_id}/groups/assign`](endpoints/crm/leads/lead/groups/UNASSIGN.md)
  - [Verify a lead's email address `GET crm/leads/{lead_id}/email/verify`](endpoints/crm/leads/lead/email/VERIFY.md)
    - [Send an email to a lead `POST crm/leads/{lead_id}/email/send`](endpoints/crm/leads/lead/email/SEND.md)
  - [Recommend a listing to a lead `POST crm/leads/{lead_id}/listing/recommend`](endpoints/crm/leads/lead/listing/RECOMMEND.md)
  - [Add a note to a lead `POST crm/leads/{lead_id}/note/add`](endpoints/crm/leads/lead/note/ADD.md)
  - [Log a phone call to a lead `POST crm/leads/{lead_id}/phone/track`](endpoints/crm/leads/lead/phone/TRACK.md)
  - [Verify a lead for text messaging `GET crm/leads/{lead_id}/text/verify`](endpoints/crm/leads/lead/text/VERIFY.md)
    - [Send a text message to a lead `POST crm/leads/{lead_id}/text/send`](endpoints/crm/leads/lead/text/SEND.md)

#### Lenders

- [Search available lenders `GET crm/lenders`](endpoints/crm/lenders/COLLECTION.md)
- [Search a lender `GET crm/lenders/{lender_id}`](endpoints/crm/lenders/lender/GET.md)

#### Active User

- [Get the user's information `GET crm/user`](endpoints/crm/user/GET.md)
- [Get the user's inbox items `GET crm/user/inbox`](endpoints/crm/user/inbox/COLLECTION.md)
  - [Dismiss a user's inbox item `POST crm/user/inbox/{item_id}/dismiss`](endpoints/crm/user/inbox/item/DISMISS.md)
- [Get a list of the user's CRM permissions `GET crm/user/permissions`](endpoints/crm/user/permissions/COLLECTION.md)
- [Get a list of the user's action plan tasks `GET crm/user/tasks`](endpoints/crm/user/tasks/COLLECTION.md)
- [Get a user's action plan task `GET crm/user/tasks/{task_id}`](endpoints/crm/user/tasks/task/GET.md)
  - [Update a user's action plan task `PUT crm/user/tasks/{task_id}`](endpoints/crm/user/tasks/task/UPDATE.md)
