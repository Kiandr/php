# GET crm/leads/{lead_id}/email/verify

Verify a lead's email address.

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| lead | (object) | Object containing lead's email verification details
| lead.email | (string) | The lead's email address
| lead.first_name | (string) | The lead's first name
| lead.id | (int) | The lead's ID #
| lead.last_name | (string) | The lead's last name
| lead.reason_not_verified | (string) | If the email address was not verified, this provides the reason
| lead.verified | (bool) | Determines whether the lead's email address was verified
