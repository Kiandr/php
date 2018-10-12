<h2 id="favorites-create">Create a new <?=Locale::spell('favorite');?></h2>

<p><code>POST http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/leads/:email/favorites</code></p>

<p>
	Creating a <?=Locale::spell('favorite');?> will associate it with the given <a href="#lead">Lead</a>. Once created, the <?=Locale::spell('favorite');?>'s identifier can be used in other requests
	such as <a href="#favorites-delete">deleting</a> or <a href="#favorites-retrieve">retrieving</a> an existing <?=Locale::spell('favorite');?>.
</p>

<p>
	The <a href="#responses">response</a> of this request contains the <a href="#favorite"><?=Locale::spell('Favorite');?></a> object that was created.
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
							The e-mail address of the lead for which you are creating a <?=Locale::spell('favorite');?>.
						</p>
					</td>
				</tr>
				<tr>
					<th>mls_number</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The listing's MLS number within the IDX feed.
						</p>
					</td>
				</tr>
				<tr>
					<th>type</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The listing's property type within the IDX feed.
						</p>
					</td>
				</tr>
				<tr>
					<th>feed</th>
					<td>
						<p><strong>string</strong></p>
						<p>The listing's IDX feed identifier.</p>
					</td>
				</tr>
				<tr>
					<th>source</th>
					<td>
						<p><strong>string</strong></p>
						<p>The listing's residing table within the IDX database.</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">Example Request</span>
<pre class="prettyprint">POST /api/crm/v1/leads/andy@realestatewebmasters.com/favorites HTTP/1.1
Host: <?=$_SERVER['HTTP_HOST'] . PHP_EOL;?>
User-Agent: myAPIClient/1.0
X-REW-API-Key: <?=$api_key . PHP_EOL;?>

<?=http_build_query(array(
	'mls_number' => '13667279',
	'type' => 'Residential',
	'feed' => 'carets',
	'source' => '_rewidx_listings',
));?>
</pre>
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