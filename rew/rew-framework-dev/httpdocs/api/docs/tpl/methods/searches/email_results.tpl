<h2 id="instant_searches-email">Email A Batch Of Instant Search Results</h2>

<p><code>POST http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/leads/searches/email_results/:feed</code></p>

<p>
	This method processes a batch of instant search results and emails the results to the respective leads.
</p>

<p>
	The <a href="#responses">response</a> of this request contains an array of Log objects.
</p>

<div class="docs-object">
	<div class="object-schema">
		<span class="title">Request Arguments</span>
		<table class="table table-fields">
			<tbody>
				<tr>
					<th><code>:feed</code></th>
					<td>
						<p><span class="label label-outline">URI Argument</span></p>
						<p>
							The feed associated with the instant search results.
						</p>
					</td>
				</tr>
				<tr>
					<th>listing_info</th>
					<td>
						<p><strong>array of object</strong></p>
						<p>
							An array consisting of IDX listing objects.
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">Example Request</span>
<pre class="prettyprint">POST /api/crm/v1/leads/searches/email_results/mfr HTTP/1.1
Host: <?=$_SERVER['HTTP_HOST'] . PHP_EOL;?>
User-Agent: myAPIClient/1.0
X-REW-API-Key: <?=$api_key . PHP_EOL;?>

<?=json_encode(array(
    array(
    	'search_ids' => array(1, 6, 90, 8705, 349494),
    	'listing' => array(
    		'id' => '1234',
    		'AddressCity' => 'Orlando',
    		'ListingPrice' => '250000',
    		'ListingImage' => 'https://6fb2e921cc34e14c15f8-2af83b1a9e85a650dd64d056ed295658.ssl.cf5.rackcdn.com/s4835143-residential-1sweebn-m.jpg',
    		'ListingMLS' => 'V234950',
            'NumberOfBedrooms' => 2,
            'NumberOfBathrooms' => 2,
            'ListingStatus' => 'Active',
            'ListingRemarks' => 'Near Disney World, around 15-20 minutes drive. Charming Ranch/ 1-story home nestled behind mature oak trees providing shade by cul-de-sac street in a quiet neighborhood. Split floor plan and vaulted ceilings give roomy feel to the home. Wood floors carry through great room, dining room, and owners bedroom areas. Sliding doors and windows open to back patio and private yard. Enjoy cost efficient gas utilities. Very pretty façade.',
            'ListingAgent' => 'Charles Simmons',
            'ListingOffice' => 'RE/MAX Orlando',
    	)
    )
), JSON_PRETTY_PRINT);?>
</pre>
	</div>
	<div class="highlight">
		<span class="title">Example Response</span>
<pre class="prettyprint">HTTP/1.1 200 OK
Content-Type: application/json

{
    "data" : [
        {
            "type" : "debug",
            "message" : "Skipping Email to david@realestatewebmasters.com.  No New Listings Found In Search 1234",
        },
        {
            "type" : "debug",
            "message" : "Successfully sent email to david@realestatewebmasters.com for search 4356",
        },
        {...},
        {...}
    ]
}</pre>
	</div>
</div>
