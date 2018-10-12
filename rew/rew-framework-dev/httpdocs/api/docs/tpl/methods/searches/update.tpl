<h2 id="searches-update">Update a saved search</h2>

<p><code>POST http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/leads/:email/searches/:id</code></p>

<p>
	This method allows you to update a single <a href="#search">Search</a> object by its identifier <code>:id</code>. Only parameters that are set
	will be updated, while the omitted parameters' values will be left unchanged.
</p>

<p>
	The <a href="#responses">response</a> of this request contains the updated <a href="#search">Search</a> object.
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
							The e-mail address of the lead for which you are updating a saved search.
						</p>
					</td>
				</tr>
				<tr>
					<th><code>:id</code></th>
					<td>
						<p><span class="label label-outline">URI Argument</span></p>
						<p>
							The record identifier of the saved search being updated.
						</p>
					</td>
				</tr>
				<tr>
					<th>title</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							The search's title.
						</p>
					</td>
				</tr>
				<tr>
					<th>criteria</th>
					<td>
						<p><strong>object</strong> <span class="label label-outline">optional</span></p>
						<p>
							An object containing the saved search criteria. This should consist of search fields as the keys and their respective values.
						</p>
					</td>
				</tr>
				<tr>
					<th>frequency</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							The frequency of email alerts for the search.
						</p>
						<p>
							<small>Accepted values: <code>never</code>, <code>daily</code>, <code>weekly</code>, <code>monthly</code></small>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">Example Request</span>
<pre class="prettyprint">POST /api/crm/v1/leads/andy@realestatewebmasters.com/searches/13 HTTP/1.1
Host: <?=$_SERVER['HTTP_HOST'] . PHP_EOL;?>
User-Agent: myAPIClient/1.0
X-REW-API-Key: <?=$api_key . PHP_EOL;?>

<?=http_build_query(array(
	'title' => 'Rental in Agoura Hills & more...',
	'criteria' => array(
		'search_city' => array('Agoura Hills', 'Agua Dulce', 'Aguanga'),
		'search_type' => array('Rental'),
		'order' => 'ListingPrice',
		'sort' => 'DESC',
		'view' => 'grid',
	),
));?>
</pre>
	</div>
	<div class="highlight">
		<span class="title">Example Response</span>
<pre class="prettyprint">HTTP/1.1 200 OK
Content-Type: application/json

{
    "data" : {
        "id" : 13,
        "title" : "Rental in Agoura Hills &amp; more...",
        "criteria" : {
            "search_city" : [
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
    }
}</pre>
	</div>
</div>