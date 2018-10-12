<h2 id="searches-create">Create a new saved search</h2>

<p><code>POST http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/leads/:email/searches</code></p>

<p>
	Creating a saved search will associate it with the given <a href="#lead">Lead</a>. Once created, the search's identifier can be used in other requests
	such as <a href="#searches-delete">deleting</a> or <a href="#searches-retrieve">retrieving</a> an existing saved search.
</p>

<p>
	The <a href="#responses">response</a> of this request contains the <a href="#search">Search</a> object that was created.
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
							The e-mail address of the lead for which you are creating a saved search.
						</p>
					</td>
				</tr>
				<tr>
					<th>title</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The search's title.
						</p>
					</td>
				</tr>
				<tr>
					<th>criteria</th>
					<td>
						<p><strong>object</strong></p>
						<p>
							An object containing the saved search criteria. This should consist of search fields as the keys and their respective values.
						</p>
					</td>
				</tr>
				<tr>
					<th>feed</th>
					<td>
						<p><strong>string</strong></p>
						<p>The IDX feed identifier that the search is for.</p>
					</td>
				</tr>
				<tr>
					<th>source</th>
					<td>
						<p><strong>string</strong></p>
						<p>The table within the IDX database that should be searched.</p>
					</td>
				</tr>
				<tr>
					<th>frequency</th>
					<td>
						<p><strong>string</strong> <span class="label label-outline">optional</span></p>
						<p>
							The frequency of email alerts for the search. If not specified, the value <code>weekly</code> is assumed.
						</p>
						<p>
							<small>Accepted values: <code>never</code>, <code>daily</code>, <code>weekly</code>, <code>monthly</code></small>
						</p>
					</td>
				</tr>
				<?php if (Settings::isREW()) { ?>
					<tr>
						<th>_suppress_alerts</th>
						<td>
							<p>
								<strong>string</strong>
								<span class="label label-outline">optional</span>
								<span class="label label-danger" title="This field is for internal REW use only. You are seeing it in this documentation because you have a REW IP.">
									private API
								</span>
							</p>
							<p>
								If set to <code>1</code>, the search will be flagged so that the destination site will not send email alerts for it.
							</p>
							<p>
								This is used by the built-in Outgoing API in order to prevent the "master" site from sending saved search emails for searches that were pushed via the API.
							</p>
							<p>
								<small>Accepted values: <code>1</code></small>
							</p>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">Example Request</span>
<pre class="prettyprint">POST /api/crm/v1/leads/andy@realestatewebmasters.com/searches HTTP/1.1
Host: <?=$_SERVER['HTTP_HOST'] . PHP_EOL;?>
User-Agent: myAPIClient/1.0
X-REW-API-Key: <?=$api_key . PHP_EOL;?>

<?=http_build_query(array(
	'title' => 'Rental in Adelanto & more...',
	'criteria' => array(
		'search_city' => array('Adelanto', 'Agoura Hills', 'Agua Dulce', 'Aguanga'),
		'search_type' => array('Rental'),
		'order' => 'ListingPrice',
		'sort' => 'DESC',
		'view' => 'grid',
	),
	'feed' => 'carets',
	'source' => '_rewidx_listings',
	'frequency' => 'weekly',
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