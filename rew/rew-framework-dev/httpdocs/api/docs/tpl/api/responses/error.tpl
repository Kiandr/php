<h2 id="responses-error">Error <small><span class="label label-warning">4XX</span> <span class="label label-danger">5XX</span></small></h2>
<p>
	Responses with the HTTP codes in the 4xx or 5xx range indicate an error. For these responses, the JSON object will contain a single <code>error</code> object
	that consists of the following properties:
</p>

<table class="table table-fields">
	<tbody>
		<tr>
			<th>type</th>
			<td>
				<p><strong>string</strong></p>
				<p>The type of error that occurred.</p>
				<p><small>Possible values: <code>invalid_request</code>, <code>internal_error</code></small></p>
			</td>
		</tr>
		<tr>
			<th>message</th>
			<td>
				<p><strong>string</strong></p>
				<p>A human-readable message that contains a description of the error.</p>
			</td>
		</tr>
		<tr>
			<th>details</th>
			<td>
				<p><strong>object</strong> <span class="label label-outline">optional</span></p>
				<p>An object containing more information about the error.</p>
			</td>
		</tr>
		<tr>
			<th>code</th>
			<td>
				<p><strong>integer</strong> <span class="label label-outline">optional</span></p>
				<p>An internal REW-specific code for the error, to help narrow down its origin.</p>
			</td>
		</tr>
	</tbody>
</table>

<strong>Example Response</strong>

<div class="highlight">
<pre class="prettyprint">HTTP/1.1 400 Bad Request
Content-Type: application/json

{
    "error" : {
        "type" : "invalid_request",
        "message" : "Required parameter is missing: 'first_name'"
    }
}</pre>
</div>