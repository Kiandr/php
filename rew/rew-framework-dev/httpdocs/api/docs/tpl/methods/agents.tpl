<div class="page-header">
	<h1 id="agents">Agents</h1>
</div>

<p>
	Agent objects describe Lead Manager agent accounts that leads can be assigned to. The API allows you to retrieve agent accounts
	within the Lead Management system.
</p>

<div class="docs-object">
	<div class="object-schema">
		<span class="title" id="agent"><a href="#agent">Agent</a> object</span>
		<table class="table table-fields">
			<tbody>
				<tr>
					<th>id</th>
					<td>
						<p><strong>integer</strong></p>
						<p>The agent's identifier within the system.</p>
					</td>
				</tr>
				<tr>
					<th>name</th>
					<td>
						<p><strong>string</strong></p>
						<p>The agent's full name.</p>
					</td>
				</tr>
				<tr>
					<th>email</th>
					<td>
						<p><strong>string</strong></p>
						<p>The agent's e-mail address.</p>
					</td>
				</tr>
				<tr>
					<th>title</th>
					<td>
						<p><strong>string</strong></p>
						<p>The agent's title or role in the office.</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">JSON Example</span>
<pre class="prettyprint">{
    "id" : 2,
    "name" : "Jane Smith",
    "email" : "jane.smith@example.com",
    "title" : "REALTOR&reg;"
}</pre>
					</div>
</div>

<hr>
<?php include __DIR__ . '/agents/list.tpl';?>