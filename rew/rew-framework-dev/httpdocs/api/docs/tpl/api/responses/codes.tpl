<h2 id="responses-codes">Status Codes</h2>

<p>
	The CRM API uses conventional HTTP response codes to indicate if a request has succeeded or failed. Below is a table of response code ranges and
	how they should be interpreted by the client.
</p>

<table class="table table-bordered">
	<thead>
		<tr>
			<th nowrap>Code Range</th>
			<th>Indicates</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><span class="label label-success">2XX</span></td>
			<td>The request was successful.</td>
		</tr>
		<tr>
			<td><span class="label label-warning">4XX</span></td>
			<td>The request failed due to a client error. This usually means that a required parameter was not provided.</td>
		</tr>
		<tr>
			<td><span class="label label-danger">5XX</span></td>
			<td>The request failed due to an error with REW's server. This usually indicates that the issue must be fixed on the server end.</td>
		</tr>
	</tbody>
</table>