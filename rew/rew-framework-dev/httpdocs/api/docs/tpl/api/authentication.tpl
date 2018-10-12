<div class="page-header">
	<h1 id="authentication">Authentication</h1>
</div>
<p>
	All requests to the CRM API must be authenticated. This is done by providing your API key via a custom <code>X-REW-API-Key</code> HTTP header. An example
	of an authenticated request is available below.
</p>

<strong>Example Request</strong>
<div class="highlight">
<pre class="prettyprint">GET /api/crm/v1/groups HTTP/1.1
Host: <?=$_SERVER['HTTP_HOST'] . PHP_EOL;?>
User-Agent: myAPIClient/1.0
X-REW-API-Key: <?=$api_key;?></pre>
</div>