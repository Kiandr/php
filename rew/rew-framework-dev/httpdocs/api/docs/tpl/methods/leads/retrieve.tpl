<h2 id="leads-retrieve">Retrieve an existing lead</h2>

<p><code>GET http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/leads/:email</code></p>

<p>
	This method allows you to request a single <a href="#lead">Lead</a> object by its e-mail address <code>:email</code>. You can use
	this to determine if a given record exists within the system before creating a history event associated with it.
</p>

<p>
	The <a href="#responses">response</a> of this request contains a <a href="#lead">Lead</a> object.
</p>

<div class="docs-object">
	<div class="object-schema">
		<span class="title">Request Arguments</span>
		<table class="table table-fields">
			<tbody>
				<tr>
					<th><code>:email</code></th>
					<td>
						<p><span class="label label-outline">URI Argument</span></p>
						<p>
							The e-mail address of the lead for which you are requesting a <a href="#lead">Lead</a> object.
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">Example Request</span>
<pre class="prettyprint">GET /api/crm/v1/leads/andy@realestatewebmasters.com HTTP/1.1
Host: <?=$_SERVER['HTTP_HOST'] . PHP_EOL;?>
User-Agent: myAPIClient/1.0
X-REW-API-Key: <?=$api_key . PHP_EOL;?></pre>
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