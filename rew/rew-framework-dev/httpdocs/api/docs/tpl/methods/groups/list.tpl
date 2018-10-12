<h2 id="groups-list">List all groups</h2>

<p><code>GET http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/groups</code></p>

<p>
	This method allows you to obtain a list of all groups in the Lead Management system, optionally filtered by an owning agent.
</p>

<p>
	The <a href="#responses">response</a> of this request contains an array of <a href="#group">Group</a> objects.
</p>

<div class="docs-object">
	<div class="object-schema">
		<span class="title">Request Arguments</span>
		<table class="table table-fields">
			<tbody>
				<tr>
					<th>agent_id</th>
					<td>
						<p><strong>integer</strong> <span class="label label-outline">optional</span></p>
						<p>
							The identifier of the Agent record for which to retrieve groups.
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">Example Request</span>
<pre class="prettyprint">GET /api/crm/v1/groups HTTP/1.1
Host: <?=$_SERVER['HTTP_HOST'] . PHP_EOL;?>
User-Agent: myAPIClient/1.0
X-REW-API-Key: <?=$api_key;?></pre>
	</div>
	<div class="highlight">
		<span class="title">Example Response</span>
<pre class="prettyprint">HTTP/1.1 200 OK
Content-Type: application/json

{
    "data" : [
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
        { ... },
        { ... }
    ]
}</pre>
	</div>
</div>