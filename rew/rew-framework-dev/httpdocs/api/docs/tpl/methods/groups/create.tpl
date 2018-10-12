<h2 id="groups-create">Create a new group</h2>

<p><code>POST http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/groups</code></p>

<p>
	Creating a group will add it to the Lead Management system. Once created, the group's identifier can be used in other requests
	such as <a href="#leads-create">creating a new lead</a> or <a href="#groups-retrieve">retrieving an existing group</a>.
</p>

<p>
	The <a href="#responses">response</a> of this request contains the <a href="#group">Group</a> object that was created.
</p>

<div class="docs-object">
	<div class="object-schema">
		<span class="title">Request Arguments</span>
		<table class="table table-fields">
			<tbody>
				<tr>
					<th>name</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The name of the group as it should appear in the Lead Manager.
						</p>
					</td>
				</tr>
				<tr>
					<th>description</th>
					<td>
						<p><strong>string</strong> <label class="label label-outline">optional</label></p>
						<p>
							The group's description.
						</p>
					</td>
				</tr>
				<tr>
					<th>agent_id</th>
					<td>
						<p><strong>integer</strong> <label class="label label-outline">optional</label></p>
						<p>
							The identifier of the <a href="#agent">Agent</a> record that should own the group. You can omit this argument or leave it blank
							to make the group global, so all agents can see and use it.
						</p>
					</td>
				</tr>
				<tr>
					<th>system</th>
					<td>
						<p><strong>string</strong> <label class="label label-outline">optional</label></p>
						<p>
							Whether this should be a system group. System groups cannot be edited or deleted by agents in the Backend - not even the Super Admin.
						</p>
						<p>
							You can omit this argument or leave it blank to create a normal group that can be edited by agents.
						</p>
						<p>
							<small>Accepted values: <code>true</code></small>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">Example Request</span>
<pre class="prettyprint">POST /api/crm/v1/groups HTTP/1.1
Host: <?=$_SERVER['HTTP_HOST'] . PHP_EOL;?>
User-Agent: myAPIClient/1.0
X-REW-API-Key: <?=$api_key . PHP_EOL;?>

<?=http_build_query(array(
	'name' => 'Guaranteed Sale Leads',
	'description' => 'Leads submitted through the Guaranteed Sale site',
	'agent_id' => 2,
));?>
</pre>
	</div>
	<div class="highlight">
		<span class="title">Example Response</span>
<pre class="prettyprint">HTTP/1.1 200 OK
Content-Type: application/json

{
    "data" : {
        "id" : 168,
        "agent_id" : 2,
        "name" : "Guaranteed Sale Leads",
        "description" : "Leads submitted through the Guaranteed Sale site",
        "system" : false,
        "timestamp" : 1340598400
    }
}</pre>
	</div>
</div>