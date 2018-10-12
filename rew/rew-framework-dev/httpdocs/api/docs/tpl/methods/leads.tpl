<div class="page-header">
	<h1 id="leads">Leads</h1>
</div>

<p>
	Lead objects describe Lead Manager leads that can be assigned to <a href="#agent">Agents</a> and <a href="#groups">Groups</a>.
	The API allows you to create and update leads within the Lead Management system.
</p>

<div class="docs-object">
	<div class="object-schema">
		<span class="title" id="lead"><a href="#lead">Lead</a> object</span>
		<table class="table table-fields">
			<tbody>
				<tr>
					<th>id</th>
					<td>
						<p><strong>integer</strong></p>
						<p>
							The lead's identifier within the system.
						</p>
					</td>
				</tr>
				<tr>
					<th>agent</th>
					<td>
						<p><strong><a href="#agent">Agent</a></strong></p>
						<p>
							The agent that the lead is assigned to.
						</p>
					</td>
				</tr>
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
                        <p><strong>string</strong></p>
                        <p>
                            The lead's alternate e-mail address.
                        </p>
                    </td>
                </tr>
				<tr>
					<th>groups</th>
					<td>
						<p><strong><a href="#group">Group[]</a></strong></p>
						<p>
							An array of <a href="#group">Group</a> objects representing the Lead Manager groups that the lead is assigned to.
						</p>
					</td>
				</tr>
				<tr>
					<th>address</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The lead's physical address.
						</p>
					</td>
				</tr>
				<tr>
					<th>city</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The lead's city.
						</p>
					</td>
				</tr>
				<tr>
					<th>state</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The lead's state.
						</p>
					</td>
				</tr>
				<tr>
					<th>zip</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The lead's ZIP/Postal code.
						</p>
					</td>
				</tr>
				<tr>
					<th>phone</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The lead's phone number.
						</p>
					</td>
				</tr>
                <tr>
                    <th>phone_cell</th>
                    <td>
                        <p><strong>string</strong></p>
                        <p>
                            The lead's secondary phone number.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th>phone_work</th>
                    <td>
                        <p><strong>string</strong></p>
                        <p>
                            The lead's work phone number.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th>phone_fax</th>
                    <td>
                        <p><strong>string</strong></p>
                        <p>
                            The lead's fax number.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th>contact_method</th>
                    <td>
                        <p><strong>string</strong></p>
                        <p>
                            The lead's preferred contact method.
                        </p>
                        <p>
                            <small>Possible values: <code>email</code>, <code>phone</code>, <code>text</code></small>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th>heat</th>
                    <td>
                        <p><strong>string</strong></p>
                        <p>
                            The lead's heat.
                        </p>
                        <p>
                            <small>Possible values: <code></code>, <code>hot</code>, <code>mediumhot</code>, <code>warm</code>, <code>lukewarm</code>, <code>cold</code></small>
                        </p>
                    </td>
                </tr>
				<tr>
					<th>comments</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The lead's last form submission comments.
						</p>
					</td>
				</tr>
				<tr>
					<th>origin</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The lead's last visit referer.
						</p>
					</td>
				</tr>
				<tr>
					<th>keywords</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The lead's last search engine keywords.
						</p>
					</td>
				</tr>
				<tr>
					<th>opt_marketing</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							Whether the lead has opted into mass e-mails &amp; campaign e-mails.
						</p>
						<p><small>Possible values: <code>in</code>, <code>out</code></small></p>
					</td>
				</tr>
				<tr>
					<th>opt_searches</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							Whether the lead has opted into saved search alert e-mails.
						</p>
						<p><small>Possible values: <code>in</code>, <code>out</code></small></p>
					</td>
				</tr>
                <tr>
                    <th>opt_texts</th>
                    <td>
                        <p><strong>string</strong></p>
                        <p>
                            Whether the lead should be opted into text messages. If not specified, the value <code>out</code> is assumed.
                        </p>
                        <p>
                            <small>Possible values: <code>in</code>, <code>out</code></small>
                        </p>
                    </td>
                </tr>
				<tr>
					<th>auto_rotate</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							Whether the lead is allowed to be auto-rotated away from their current agent.
							If set to <code>true</code> and auto-rotation is on, the lead will be rotated to the next agent if they haven't been accepted in time.
						</p>
						<p><small>Possible values: <code>true</code>, <code>false</code></small></p>
					</td>
				</tr>
				<tr>
					<th>source_user_id</th>
					<td>
						<p><strong>integer</strong></p>
						<p>
							The identifier for this record within the third-party system that created the lead.
						</p>
					</td>
				</tr>
				<tr>
					<th>num_visits</th>
					<td>
						<p><strong>integer</strong></p>
						<p>
							The number of distinct visit sessions started by the lead.
						</p>
					</td>
				</tr>
				<tr>
					<th>timestamp</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							A UNIX timestamp (in UTC) of when the lead was created.
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">JSON Example</span>
<pre class="prettyprint">{
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
    "comments" : "Hello! Here is my form submission.",
    "origin" : "guaranteedsale.com",
    "keywords" : null,
    "timestamp" : <?=time() . PHP_EOL;?>
}</pre>
	</div>
</div>

<hr>
<?php include __DIR__ . '/leads/create.tpl';?>
<hr>
<?php include __DIR__ . '/leads/retrieve.tpl';?>
<hr>
<?php include __DIR__ . '/leads/update.tpl';?>
<hr>
<?php include __DIR__ . '/leads/upsert.tpl';?>
