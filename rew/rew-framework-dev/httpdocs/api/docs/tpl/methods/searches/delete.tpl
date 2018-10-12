<h2 id="searches-delete">Delete a saved search</h2>

<p><code>DELETE http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/leads/:email/searches/:id</code></p>

<p>
	This method allows you to delete a single <a href="#search">Search</a> object by its record identifier <code>:id</code>.
</p>

<p>
	The <a href="#responses">response</a> of this request contains the <a href="#search">Search</a> object that was deleted.
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
							The e-mail address of the lead for which you are deleting a saved search.
						</p>
					</td>
				</tr>
				<tr>
					<th><code>:id</code></th>
					<td>
						<p><span class="label label-outline">URI Argument</span></p>
						<p>
							The record identifier of the saved search that should be deleted.
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">Example Request</span>
<pre class="prettyprint">DELETE /api/crm/v1/leads/andy@realestatewebmasters.com/searches/13 HTTP/1.1
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
        "times_sent" : 0,
        "feed" : "carets",
        "source" : "_rewidx_listings",
        "timestamp_sent" : null,
        "timestamp" : <?=time() . PHP_EOL;?>
    }
}</pre>
	</div>
</div>