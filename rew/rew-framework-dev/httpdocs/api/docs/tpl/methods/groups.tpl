<div class="page-header">
	<h1 id="groups">Groups</h1>
</div>

<p>
	Group objects describe Lead Manager groups that leads can be assigned to. The API allows you to create and retrieve groups within
	the Lead Management system.
</p>

<div class="docs-object">
	<div class="object-schema">
		<span class="title" id="group"><a href="#group">Group</a> object</span>
		<table class="table table-fields">
			<tbody>
				<tr>
					<th>id</th>
					<td>
						<p><strong>integer</strong></p>
						<p>The group's identifier within the system.</p>
					</td>
				</tr>
				<tr>
					<th>agent_id</th>
					<td>
						<p><strong>integer</strong></p>
						<p>The identifier of the <a href="#agent">Agent</a> record that owns the group. This will be <code>null</code> if the group is global.</p>
					</td>
				</tr>
				<tr>
					<th>name</th>
					<td>
						<p><strong>string</strong></p>
						<p>The group's name.</p>
					</td>
				</tr>
				<tr>
					<th>description</th>
					<td>
						<p><strong>string</strong></p>
						<p>The group's description.</p>
					</td>
				</tr>
				<tr>
					<th>system</th>
					<td>
						<p><strong>boolean</strong></p>
						<p>Whether this is a system-created group that cannot be modified.</p>
					</td>
				</tr>
				<tr>
					<th>timestamp</th>
					<td>
						<p><strong>integer</strong></p>
						<p>A UNIX timestamp (in UTC) of when the group was created.</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">JSON Example</span>
<pre class="prettyprint">{
    "id" : 154,
    "agent_id" : null,
    "name" : "Buyer Drip",
    "description" : "Group that triggers the buyer drip campaign",
    "system" : false,
    "timestamp" : 1340588400
}</pre>
	</div>
</div>

<hr>
<?php include __DIR__ . '/groups/create.tpl';?>
<hr>
<?php include __DIR__ . '/groups/retrieve.tpl';?>
<hr>
<?php include __DIR__ . '/groups/delete.tpl';?>
<hr>
<?php include __DIR__ . '/groups/list.tpl';?>
