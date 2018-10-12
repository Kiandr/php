<div class="page-header">
	<h1 id="searches">Saved Searches</h1>
</div>

<p>
	Saved Searches are owned by <a href="#lead">Lead</a> objects and represent search criteria that can be used to match listings in the IDX.
	The API allows you to list, create, update, and delete saved searches associated with a Lead.
</p>

<div class="docs-object">
	<div class="object-schema">
		<span class="title" id="search"><a href="#search">Search</a> object</span>
		<table class="table table-fields">
			<tbody>
				<tr>
					<th>id</th>
					<td>
						<p><strong>integer</strong></p>
						<p>The saved search's identifier within the system.</p>
					</td>
				</tr>
				<tr>
					<th>title</th>
					<td>
						<p><strong>string</strong></p>
						<p>The search's title.</p>
					</td>
				</tr>
				<tr>
					<th>criteria</th>
					<td>
						<p><strong>object</strong></p>
						<p>An object containing arbitrary search fields as the keys and their respective values.</p>
					</td>
				</tr>
				<tr>
					<th>frequency</th>
					<td>
						<p><strong>string</strong></p>
						<p>The frequency of email alerts for the search.</p>
						<p>
							<small>Possible values: <code>never</code>, <code>daily</code>, <code>weekly</code>, <code>monthly</code></small>
						</p>
					</td>
				</tr>
				<tr>
					<th>times_sent</th>
					<td>
						<p><strong>integer</strong></p>
						<p>The number of times an alert email about this search was sent to the lead.</p>
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
					<th>timestamp_sent</th>
					<td>
						<p><strong>integer</strong></p>
						<p>A UNIX timestamp (in UTC) of when an alert for the search was last sent.</p>
					</td>
				</tr>
				<tr>
					<th>timestamp</th>
					<td>
						<p><strong>integer</strong></p>
						<p>A UNIX timestamp (in UTC) of when the search was created.</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">JSON Example</span>
<pre class="prettyprint">{
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
}</pre>
	</div>
</div>

<hr>
<?php include __DIR__ . '/searches/create.tpl';?>
<hr>
<?php include __DIR__ . '/searches/retrieve.tpl';?>
<hr>
<?php include __DIR__ . '/searches/update.tpl';?>
<hr>
<?php include __DIR__ . '/searches/delete.tpl';?>
<hr>
<?php include __DIR__ . '/searches/list.tpl';?>
