<h2 id="groups-retrieve">Retrieve an existing group</h2>

<p><code>GET http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/groups/:id</code></p>

<p>
	This method allows you to request a single <a href="#group">Group</a> object by its record identifier <code>:id</code>. You can use
	this to determine if a given record exists within the system.
</p>

<p>
	The <a href="#responses">response</a> of this request contains a <a href="#group">Group</a> object.
</p>

<div class="docs-object">
	<div class="object-schema">
		<span class="title">Request Arguments</span>
		<table class="table table-fields">
			<tbody>
				<tr>
					<th><code>:id</code></th>
					<td>
						<p><span class="label label-outline">URI Argument</span></p>
						<p>
							The record identifier of the group for which you are requesting a <a href="#group">Group</a> object.
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">Example Request</span>
<pre class="prettyprint">GET /api/crm/v1/groups/168 HTTP/1.1
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