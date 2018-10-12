<h2 id="instant_searches-list">Request A Batch Of Instant Searches</h2>

<p><code>GET http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/leads/searches/instant/:feed/:id(/:limit)</code></p>

<p>
	This method allows you to obtain a list of instant searches for a given feed starting at a given instant search ID from the Lead Management system.
</p>

<p>
	The <a href="#responses">response</a> of this request contains an array of <a href="#instant_search">Instant Search</a> objects.
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
							The IDX feed of the instant searches you are requesting.
						</p>
					</td>
				</tr>
				<tr>
					<th><code>:id</code></th>
					<td>
						<p><span class="label label-outline">URI Argument</span></p>
						<p>
							The starting instant search ID of requested batch of instant search objects.
						</p>
					</td>
				</tr>
				<tr>
					<th><code>:limit</code></th>
					<td>
						<p><span class="label label-outline">URI Argument</span></p>
						<p>
							Optional argument setting the maximum number of instant search objects to be returned.
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">Example Request</span>
<pre class="prettyprint">GET /api/crm/v1/leads/searches/searches/mfr/0 HTTP/1.1
Host: <?=$_SERVER['HTTP_HOST'] . PHP_EOL;?>
User-Agent: myAPIClient/1.0
X-REW-API-Key: <?=$api_key . PHP_EOL;?>
</pre>
	</div>
	<div class="highlight">
		<span class="title">Example Response</span>
<pre class="prettyprint">HTTP/1.1 200 OK
Content-Type: application/json

{
	"data" : [
		{
			"id" : 168,
			"search_query" => {
				"select"   => "SELECT `t1`.`id` AS 'id', `t1`.`ListingMLS` AS 'ListingMLS', `t1`.`ListingPrice` AS 'ListingPrice', `t1`.`ListingStatus` AS 'ListingStatus', `t1`.`ListingRemarks` AS 'ListingRemarks', `t1`.`ListingImage` AS 'ListingImage', `t1`.`AddressCity` AS 'AddressCity', `t1`.`NumberOfBedrooms` AS 'NumberOfBedrooms', `t1`.`NumberOfBathrooms` AS 'NumberOfBathrooms', `t1`.`ListingOffice` AS 'ListingOffice', `t1`.`ListingAgent` AS 'ListingAgent'",
				"where"    => "WHERE `t1`.`ListingPrice` <= '200000' AND `t1`.`ListingDOM` <= '7'",
				"order"    => "ORDER BY `t1`.`timestamp_created` DESC",
				"limit"    => "LIMIT 10",
				"polygons" => true
			}
		},
		{
			"id" : 204,
			"search_query" => {
				"select"   => "SELECT `t1`.`id` AS 'id', `t1`.`ListingMLS` AS 'ListingMLS', `t1`.`ListingPrice` AS 'ListingPrice', `t1`.`ListingStatus` AS 'ListingStatus', `t1`.`ListingRemarks` AS 'ListingRemarks', `t1`.`ListingImage` AS 'ListingImage', `t1`.`AddressCity` AS 'AddressCity', `t1`.`NumberOfBedrooms` AS 'NumberOfBedrooms', `t1`.`NumberOfBathrooms` AS 'NumberOfBathrooms', `t1`.`ListingOffice` AS 'ListingOffice', `t1`.`ListingAgent` AS 'ListingAgent'",
				"where"    => "WHERE `t1`.`ListingPrice` <= '350000'",
				"order"    => "ORDER BY `t1`.`timestamp_created` DESC",
				"limit"    => "LIMIT 10",
				"polygons" => true
			}
		},
		{...},
		{...}
	]
}</pre>
	</div>
</div>
