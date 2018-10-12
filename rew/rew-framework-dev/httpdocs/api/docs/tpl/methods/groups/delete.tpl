<h2 id="groups-delete">Delete a group</h2>

<p><code>DELETE http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/groups/:id</code></p>

<p>
	This method allows you to delete a single <a href="#group">Group</a> object by its record identifier <code>:id</code>.
	Any leads that are assigned to the group will be un-assigned prior to deletion.
</p>

<p>
	The <a href="#responses">response</a> of this request contains the <a href="#group">Group</a> object that was deleted.
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
							The record identifier of the group that should be deleted.
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">Example Request</span>
<pre class="prettyprint">DELETE /api/crm/v1/groups/168 HTTP/1.1
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