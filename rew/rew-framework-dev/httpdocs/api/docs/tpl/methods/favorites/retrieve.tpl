<h2 id="favorites-retrieve">Retrieve an existing <?=Locale::spell('favorite');?></h2>

<p><code>GET http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/leads/:email/favorites/:id</code></p>

<p>
	This method allows you to request a single <a href="#favorite"><?=Locale::spell('Favorite');?></a> object by its record identifier <code>:id</code>. You can use
	this to determine if a given record exists within the system.
</p>

<p>
	The <a href="#responses">response</a> of this request contains a <a href="#favorite">Favorite</a> object.
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
							The e-mail address of the lead for which you are retrieving a <?=Locale::spell('favorite');?>.
						</p>
					</td>
				</tr>
				<tr>
					<th><code>:id</code></th>
					<td>
						<p><span class="label label-outline">URI Argument</span></p>
						<p>
							The record identifier of the <?=Locale::spell('favorite');?> being requested.
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">Example Request</span>
<pre class="prettyprint">GET /api/crm/v1/leads/andy@realestatewebmasters.com/favorites/16 HTTP/1.1
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
    }
}</pre>
	</div>
</div>