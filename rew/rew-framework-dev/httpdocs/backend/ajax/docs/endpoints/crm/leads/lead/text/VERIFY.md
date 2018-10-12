# GET crm/leads/{lead_id}/text/verify

Verify a lead's cell phone number for text messaging

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| lead | (object) | Contains the lead text verification data
| lead.first_name | (string) | The lead's first name
| lead.id | (int) | The lead's ID #
| lead.last_name | (string) | The lead's last name
| lead.opted_in| (bool) | Flags whether the lead is opted in to receiving text messages from the CRM
| lead.phone_cell | (string) | The lead's cell phone #
| lead.verification_error | (string) | Contains error details if the lead's cell phone # couldn't be verified
| lead.verified | (bool) | Flags whether the lead's cell phone # was verified
