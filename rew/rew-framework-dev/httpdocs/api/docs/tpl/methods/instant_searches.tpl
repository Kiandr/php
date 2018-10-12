<?php
// This Section Is For REW Staff Only
if (!Settings::isREW()) return;
?>

<div class="page-header">
	<h1 id="instant_searches">Instant Searches</h1>
</div>

<p>
	<span class="label label-danger" title="This section is for internal REW use only. You are seeing it in this documentation because you have a REW IP.">
		private API
	</span>
</p>

<p>
	Instant Searches are owned by <a href="#lead">Lead</a> objects.
	The API allows the instant search service to list, email and update, instant searches associated with this website.
	It's worth noting that the actions presented here are done in batches to keep the number of requests down to a minimum as the instant search service executes often.
</p>

<div class="docs-object">
	<div class="object-schema">
		<span class="title" id="instant_search"><a href="#instant_search">Instant Search</a> object</span>
		<table class="table table-fields">
			<tbody>
				<tr>
					<th>id</th>
					<td>
						<p><strong>integer</strong></p>
						<p>The instant saved search's identifier within the system.</p>
					</td>
				</tr>
				<tr>
					<th>search_query</th>
					<td>
						<p><strong>object</strong></p>
						<p>An object containing the following search query components:</p>
						<tr>
							<th>select</th>
							<td>
								<p><strong>string</strong></p>
								<p>The select statement the instant search service will use to search for new listings.</p>
							</td>
						</tr>
						<tr>
							<th>where</th>
							<td>
								<p><strong>string</strong></p>
								<p>The where clause the instant search service will use to search for new listings.</p>
							</td>
						</tr>
						<tr>
							<th>order</th>
							<td>
								<p><strong>string</strong></p>
								<p>The order by section of the instant search's query.</p>
							</td>
						</tr>
						<tr>
							<th>limit</th>
							<td>
								<p><strong>string</strong></p>
								<p>The number of listings the instant search can have.</p>
							</td>
						</tr>
						<tr>
							<th>polygons</th>
							<td>
								<p><strong>boolean</strong></p>
								<p>Determinent on whether the instant serach is a polygon search.</p>
							</td>
						</tr>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">JSON Example</span>
<pre class="prettyprint">{
	"id" : 168,
	"search_query" => {
		"select"   => "SELECT `t1`.`id` AS 'id', `t1`.`ListingMLS` AS 'ListingMLS', `t1`.`ListingPrice` AS 'ListingPrice', `t1`.`ListingStatus` AS 'ListingStatus', `t1`.`ListingRemarks` AS 'ListingRemarks', `t1`.`ListingImage` AS 'ListingImage', `t1`.`AddressCity` AS 'AddressCity', `t1`.`NumberOfBedrooms` AS 'NumberOfBedrooms', `t1`.`NumberOfBathrooms` AS 'NumberOfBathrooms', `t1`.`ListingOffice` AS 'ListingOffice', `t1`.`ListingAgent` AS 'ListingAgent'",
		"where"    => "WHERE `t1`.`ListingPrice` <= '200000' AND `t1`.`ListingDOM` <= '7'",
		"order"    => "ORDER BY `t1`.`timestamp_created` DESC",
		"limit"    => "LIMIT 10",
		"polygons" => true
	}
}</pre>
	</div>
</div>

<hr>
<?php include __DIR__ . '/searches/instant.tpl';?>
<hr>
<?php include __DIR__ . '/searches/email_results.tpl';?>
