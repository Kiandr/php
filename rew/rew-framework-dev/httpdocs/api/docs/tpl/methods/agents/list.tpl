<h2 id="agents-list">List all agents</h2>

<p><code>GET http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/agents</code></p>

<p>
	This method allows you to obtain a list of all agents in the Lead Management system.
</p>

<p>
	The <a href="#responses">response</a> of this request contains an array of <a href="#agent">Agent</a> objects.
</p>

<div class="docs-object">
	<div class="object-schema">
		<span class="title">Request Arguments</span>
		<div class="center"><em>No Arguments</em></div>
	</div>
	<div class="highlight">
		<span class="title">Example Request</span>
<pre class="prettyprint">GET /api/crm/v1/agents HTTP/1.1
Host: <?=$_SERVER['HTTP_HOST'] . PHP_EOL;?>
User-Agent: myAPIClient/1.0
X-REW-API-Key: <?=$api_key;?></pre>
	</div>
	<div class="highlight">
		<span class="title">Example Response</span>
<pre class="prettyprint">{
    "data" : [
        {
            "id" : 1,
            "name" : "John Smith",
            "email" : "john.smith@example.com",
            "title" : "Broker / Owner"
        },
        {
            "id" : 2,
            "name" : "Jane Smith",
            "email" : "jane.smith@example.com",
            "title" : "REALTOR&reg;"
        },
        { ... },
        { ... }
    ]
}</pre>
	</div>
</div>