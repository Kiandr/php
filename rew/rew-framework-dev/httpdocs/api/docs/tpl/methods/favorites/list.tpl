<h2 id="favorites-list">List all <?=Locale::spell('favorites');?></h2>

<p><code>GET http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/leads/:email/favorites</code></p>

<p>
	This method allows you to obtain a list of all <?=Locale::spell('favorites');?> for a given <a href="#lead">Lead</a> in the Lead Management system.
</p>

<p>
	The <a href="#responses">response</a> of this request contains an array of <a href="#favorite"><?=Locale::spell('Favorite');?></a> objects.
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
							The e-mail address of the lead for which you are retrieving <?=Locale::spell('favorites');?>.
						</p>
					</td>
				</tr>
				<tr>
					<th>feed</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							The IDX feed identifier. If you provide this field, only <?=Locale::spell('favorites');?> for the given feed will be returned.
						</p>
					</td>
				</tr>
				<tr>
					<th>mls_number</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							The listing MLS number. If you provide this field, only <?=Locale::spell('favorites');?> with the specified MLS number will be returned.
						</p>
					</td>
				</tr>
				<tr>
					<th>type</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							The listing property type. If you provide this field, only <?=Locale::spell('favorites');?> with the specified property type will be returned.
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">Example Request</span>
<pre class="prettyprint">GET /api/crm/v1/leads/andy@realestatewebmasters.com/favorites HTTP/1.1
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
            "id" : 16,
            "mls_number" : "13667279",
            "type" : "Residential",
            "price" : 54000000,
            "city" : "Malibu",
            "subdivision" : "",
            "num_bedrooms" : 13,
            "num_bathrooms" : 14,
            "num_sqft" : 16107,
            "feed" : "carets",
            "source" : "_rewidx_listings"
        },
        {
            "id" : 19,
            "mls_number" : "13683341",
            "type" : "Residential",
            "price" : 53000000,
            "city" : "Rolling Hills",
            "subdivision" : "",
            "num_bedrooms" : 9,
            "num_bathrooms" : 25,
            "num_sqft" : null,
            "feed" : "carets",
            "source" : "_rewidx_listings"
        },
        { ... },
        { ... }
    ]
}</pre>
	</div>
</div>