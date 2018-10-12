<h2 id="searches-list">List all saved searches</h2>

<p><code>GET http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/leads/:email/searches</code></p>

<p>
	This method allows you to obtain a list of all saved searches for a given <a href="#lead">Lead</a> in the Lead Management system.
</p>

<p>
	The <a href="#responses">response</a> of this request contains an array of <a href="#search">Search</a> objects.
</p>

<div class="docs-object">
	<div class="object-schema">
		<span class="title">Request Arguments</span>
		<table class="table table-fields">
			<tbody>
				<tr>
					<th><code>:email</code></th>
					<td>
						<p><span class="label label-outline">URI Argument</span></p>
						<p>
							The e-mail address of the lead for which you are retrieving saved searches.
						</p>
					</td>
				</tr>
				<tr>
					<th>feed</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							The IDX feed identifier. If you provide this field, only saved searches for the given feed will be returned.
						</p>
					</td>
				</tr>
				<tr>
					<th>title</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							The search title. If you provide this field, only saved searches with the specified title will be returned.
						</p>
					</td>
				</tr>
				<tr>
					<th>criteria</th>
					<td>
						<p><strong>object</strong> <span class="label label-outline">optional</span></p>
						<p>
							The search criteria. If you provide this field, only saved searches with the specified criteria will be returned.
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">Example Request</span>
<pre class="prettyprint">GET /api/crm/v1/leads/andy@realestatewebmasters.com/searches HTTP/1.1
Host: <?=$_SERVER['HTTP_HOST'] . PHP_EOL;?>
User-Agent: myAPIClient/1.0
X-REW-API-Key: <?=$api_key;?></pre>
	</div>
	<div class="highlight">
		<span class="title">Example Response</span>
<pre class="prettyprint">HTTP/1.1 200 OK
Content-Type: application/json

{
    "data" : [
        {
            "id" : 13,
            "title" : "Rental in Adelanto &amp; more...",
            "criteria" : {
                "search_city" : [
                    "Adelanto",
                    "Agoura Hills",
                    "Agua Dulce",
                    "Aguanga"
                ],
                "search_type" : [
                    "Rental"
                ],
                "order" : "ListingPrice",
                "sort" : "DESC",
                "view" : "grid"
            },
            "frequency" : "weekly",
            "times_sent" : 1,
            "feed" : "carets",
            "source" : "_rewidx_listings",
            "timestamp_sent" : <?=strtotime('2013-12-12 11:03:55');?>,
            "timestamp" : <?=time() . PHP_EOL;?>
        },
        {
            "id" : 14,
            "title" : "Residential in Atwater",
            "criteria" : {
                "search_city" : [
                    "Atwater",
                ],
                "search_type" : [
                    "Residential"
                ],
                "order" : "ListingPrice",
                "sort" : "DESC",
                "view" : "grid"
            },
            "frequency" : "weekly",
            "times_sent" : 0,
            "feed" : "carets",
            "source" : "_rewidx_listings",
            "timestamp_sent" : null,
            "timestamp" : <?=strtotime('-6 days') . PHP_EOL;?>
        },
        { ... },
        { ... }
    ]
}</pre>
	</div>
</div>