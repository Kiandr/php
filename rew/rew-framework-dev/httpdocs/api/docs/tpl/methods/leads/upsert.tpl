<h2 id="leads-upsert">Create or Update a lead</h2>

<p><code>PUT http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/leads</code></p>

<p>
    This method allows you to create a new <a href="#lead">Lead</a> object or update one if it already exists. Only parameters that are set
    will be updated, while the omitted parameters' values will be left unchanged.
</p>

<p>
    Changes to field values made via this API will be automatically tracked as History events in the Lead Manager.
</p>

<p>
    Since e-mail addresses uniquely identify leads within the Lead Manager, the <strong>email</strong> field cannot be updated via the API.
</p>

<p>
    The <a href="#responses">response</a> of this request contains the updated <a href="#lead">Lead</a> object that was created or updated.
</p>

<div class="docs-object">
	<div class="object-schema">
		<span class="title">Request Arguments</span>
		<table class="table table-fields">
			<tbody>
				<tr>
					<th>first_name</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The lead's first name.
						</p>
					</td>
				</tr>
				<tr>
					<th>last_name</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The lead's last name.
						</p>
					</td>
				</tr>
				<tr>
					<th>email</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The lead's e-mail address.
						</p>
					</td>
				</tr>
                <tr>
                    <th>email_alt</th>
                    <td>
                        <p><strong>string</strong> <span class="label label-outline">optional</span></p>
                        <p>
                            The lead's alternate e-mail address.
                        </p>
                    </td>
                </tr>
				<tr>
					<th>agent_id</th>
					<td>
						<p><strong>integer</strong> <span class="label label-outline">optional</span></p>
						<p>
							The identifier of the <a href="#agent">Agent</a> record that the lead should be assigned to.
							To assign the lead to the Super Admin, set this to <code>1</code>.
							To let the site automatically determine the agent (taking auto-assign settings into account), omit this field or send a blank value.
						</p>
					</td>
				</tr>
				<tr>
					<th>password</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							The lead's password for use when logging into the IDX. This is only required if the IDX portion of the CRM
							is set up to require passwords for user accounts.
						</p>
					</td>
				</tr>
				<tr>
					<th>address</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							The lead's physical address.
						</p>
					</td>
				</tr>
				<tr>
					<th>city</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							The lead's city.
						</p>
					</td>
				</tr>
				<tr>
					<th>state</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							The lead's state.
						</p>
					</td>
				</tr>
				<tr>
					<th>zip</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							The lead's ZIP/Postal code.
						</p>
					</td>
				</tr>
				<tr>
					<th>phone</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							The lead's primary phone number.
						</p>
					</td>
				</tr>
                <tr>
                    <th>phone_cell</th>
                    <td>
                        <p><strong>string</strong> <span class="label label-outline">optional</span></p>
                        <p>
                            The lead's secondary phone number.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th>phone_work</th>
                    <td>
                        <p><strong>string</strong> <span class="label label-outline">optional</span></p>
                        <p>
                            The lead's work phone number.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th>phone_fax</th>
                    <td>
                        <p><strong>string</strong> <span class="label label-outline">optional</span></p>
                        <p>
                            The lead's fax number.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th>contact_method</th>
                    <td>
                        <p><strong>string</strong> <span class="label label-outline">optional</span></p>
                        <p>
                            The lead's preferred contact method.
                        </p>
                        <p>
                            <small>Accepted values: <code>email</code>, <code>phone</code>, <code>text</code></small>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th>heat</th>
                    <td>
                        <p><strong>string</strong> <span class="label label-outline">optional</span></p>
                        <p>
                            The lead's heat.
                        </p>
                        <p>
                            <small>Accepted values: <code></code>, <code>hot</code>, <code>mediumhot</code>, <code>warm</code>, <code>lukewarm</code>, <code>cold</code></small>
                        </p>
                    </td>
                </tr>
				<tr>
					<th>comments</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							The lead's comments or message.
						</p>
						<p>
 							You can use this field if you're creating the lead as a result of a form submission. For example, if you have a form that accepts
 							a Message or Comments, set this argument's value to the submitted text to have it appear in the lead's Summary page in the Lead Manager.
						</p>
					</td>
				</tr>
				<tr>
					<th>origin</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							The lead's origin/referer. This is typically a domain name, like <em>google.com</em>. You can use this field to indicate where the lead came from.
						</p>
					</td>
				</tr>
				<tr>
					<th>keywords</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							The lead's search engine keywords.
						</p>
					</td>
				</tr>
				<tr>
					<th>opt_marketing</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							Whether the lead should be opted into mass e-mails &amp; campaign e-mails. If not specified, the value <code>out</code> is assumed.
						</p>
						<p>
							<small>Accepted values: <code>in</code>, <code>out</code></small>
						</p>
					</td>
				</tr>
				<tr>
					<th>opt_searches</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							Whether the lead should be opted into saved search alert e-mails. If not specified, the value <code>out</code> is assumed.
						</p>
						<p>
							<small>Accepted values: <code>in</code>, <code>out</code></small>
						</p>
					</td>
				</tr>
                <tr>
                    <th>opt_texts</th>
                    <td>
                        <p><strong>string</strong> <span class="label label-outline">optional</span></p>
                        <p>
                            Whether the lead should be opted into text messages. If not specified, the value <code>out</code> is assumed.
                        </p>
                        <p>
                            <small>Accepted values: <code>in</code>, <code>out</code></small>
                        </p>
                    </td>
                </tr>
				<tr>
					<th>groups</th>
					<td>
						<p><strong>integer[]</strong> <span class="label label-outline">optional</span></p>
						<p>
							An array of integers that represent <a href="#group">Group</a> identifiers of groups that the lead should be assigned to.
						</p>
					</td>
				</tr>
				<tr>
					<th>auto_rotate</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							Whether the lead is allowed to be auto-rotated away from their current agent.
							If set to <code>true</code> and auto-rotation is on, the lead will be rotated to the next agent if they haven't been accepted in time.
						</p>
						<p>
							If not specified, the value <code>false</code> is assumed.
						</p>
						<p><small>Accepted values: <code>true</code>, <code>false</code></small></p>
					</td>
				</tr>
				<tr>
					<th>source_user_id</th>
					<td>
						<p><strong>integer <span class="label label-outline">optional</span></strong></p>
						<p>
							The identifier for this record within the third-party system that is creating the lead.
						</p>
						<p>
							If you are creating leads via the API that already exist in another system or database, you can set this identifier
							for your own reference.
						</p>
					</td>
				</tr>
				<tr>
					<th>num_visits</th>
					<td>
						<p><strong>integer <span class="label label-outline">optional</span></strong></p>
						<p>
							The number of distinct visit sessions started by the lead. If not specified, the value <code>0</code> is assumed.
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">Example Request</span>
<pre class="prettyprint">PUT /api/crm/v1/leads HTTP/1.1
Host: <?=$_SERVER['HTTP_HOST'] . PHP_EOL;?>
User-Agent: myAPIClient/1.0
X-REW-API-Key: <?=$api_key . PHP_EOL;?>

<?=http_build_query(array(
	'agent_id' => 2,
	'first_name' => 'Andy',
	'last_name' => 'REW',
	'email' => 'andy@realestatewebmasters.com',
	'address' => '223 Commercial Street',
	'city' => 'Nanaimo',
	'state' => 'BC',
	'zip' => 'V9R5G8',
	'phone' => '250-753-9893',
	'opt_marketing' => 'in',
	'groups' => array(154, 168),
	'comments' => 'Hello! Here is my form submission\'s message!',
	'origin' => 'guaranteedsale.com',
));?>
</pre>
	</div>
	<div class="highlight">
		<span class="title">Example Response</span>
<pre class="prettyprint">HTTP/1.1 200 OK
Content-Type: application/json

{
    "data" : {
        "id" : 22143
        "agent" : {
            "id" : 2,
            "name" : "Jane smith",
            "email" : "jane.smith@example.com",
            "title" : "REALTOR&reg;"
        },
        "first_name" : "REW",
        "last_name" : "Andy",
        "email" : "andy@realestatewebmasters.com",
        "groups" : [
            {
                "id" : 154,
                "agent_id" : null,
                "name" : "Buyer Drip",
                "description" : "Group that triggers the buyer drip campaign",
                "system" : false,
                "timestamp" : 1340588400
            },
            {
                "id" : 168,
                "agent_id" : 2,
                "name" : "Guaranteed Sale Leads",
                "description" : "Leads submitted through the Guaranteed Sale site",
                "system" : false,
                "timestamp" : 1340598400
            },
        ],
        "address" : "223 Commercial Street",
        "city" : "Nanaimo",
        "state" : "BC",
        "zip" : "V9R5G8",
        "opt_marketing" : "in",
        "opt_searches" : "out",
        "auto_rotate" : "false",
        "source_user_id" : null,
        "num_visits" : 3,
        "phone" : "250-753-9893",
        "comments" : "Hello! Here is my form submission's message!",
        "origin" : "guaranteedsale.com",
        "keywords" : null,
        "timestamp" : <?=time() . PHP_EOL;?>
    }
}</pre>
	</div>
</div>