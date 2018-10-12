# GET crm/email/validate

Validate a specific email address.

## Parameters

| Key | Type | Optional | Description
| - | - | - | -
| addresses | (array of strings) | no | Email addresses that will be validated

## Response 200 (application/json)

| Key | Type | Description
| - | - | -
| emails | (array) | Array of email addresses and verification results
| emails.address | (string) | The email address being verified
| emails.reason_not_valid | (string) | Error code of the issue that caused the email address to be deemed invalid
| emails.validated | (bool) | Identifies the result of the email address validation attempt
