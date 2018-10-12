<div class="page-header">
	<h1 id="overview">API Overview</h1>
</div>
<p>
	The CRM API is organized around <a target="_blank" href="http://en.wikipedia.org/wiki/Representational_State_Transfer">REST</a>. It's designed to have
	predictable, resource-oriented URLs and uses HTTP response codes to indicate the status of requests. All responses, including errors, are returned in
	<a target="_blank" href="http://www.json.org/">JSON</a> format.
</p>

<table class="table table-fields">
	<tbody>
		<tr>
			<th>API Endpoint URL</th>
			<td><code>http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/</code></td>
		</tr>
	</tbody>
</table>