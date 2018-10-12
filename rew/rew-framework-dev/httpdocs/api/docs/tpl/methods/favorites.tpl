<div class="page-header">
	<h1 id="favorites"><?=Locale::spell('Favorites');?></h1>
</div>

<p>
	<?=Locale::spell('Favorites');?> are owned by <a href="#lead">Lead</a> objects and represent listings that were saved to the lead's account.
	The API allows you to view, create and delete <?=Locale::spell('favorites');?> associated with a Lead.
</p>

<div class="docs-object">
	<div class="object-schema">
		<span class="title" id="favorite"><a href="#favorite"><?=Locale::spell('Favorite');?></a> object</span>
		<table class="table table-fields">
			<tbody>
				<tr>
					<th>id</th>
					<td>
						<p><strong>integer</strong></p>
						<p>The <?=Locale::spell('favorite');?>'s identifier within the system.</p>
					</td>
				</tr>
				<tr>
					<th>mls_number</th>
					<td>
						<p><strong>string</strong></p>
						<p>The listing's MLS number within the IDX feed.</p>
					</td>
				</tr>
				<tr>
					<th>type</th>
					<td>
						<p><strong>string</strong></p>
						<p>The listing's property type within the IDX feed.</p>
					</td>
				</tr>
				<tr>
					<th>price</th>
					<td>
						<p><strong>string</strong></p>
						<p>The listing's price.</p>
					</td>
				</tr>
				<tr>
					<th>city</th>
					<td>
						<p><strong>string</strong></p>
						<p>The listing's city.</p>
					</td>
				</tr>
				<tr>
					<th>subdivision</th>
					<td>
						<p><strong>string</strong></p>
						<p>The listing's subdivision.</p>
					</td>
				</tr>
				<tr>
					<th>num_bedrooms</th>
					<td>
						<p><strong>integer</strong></p>
						<p>The listing's bedroom count.</p>
					</td>
				</tr>
				<tr>
					<th>num_bathrooms</th>
					<td>
						<p><strong>decimal</strong></p>
						<p>The listing's bathroom count.</p>
					</td>
				</tr>
				<tr>
					<th>num_sqft</th>
					<td>
						<p><strong>integer</strong></p>
						<p>The listing's square footage.</p>
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
		<span class="title">JSON Example</span>
<pre class="prettyprint">{
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
}</pre>
	</div>
</div>

<hr>
<?php include __DIR__ . '/favorites/create.tpl';?>
<hr>
<?php include __DIR__ . '/favorites/retrieve.tpl';?>
<hr>
<?php include __DIR__ . '/favorites/delete.tpl';?>
<hr>
<?php include __DIR__ . '/favorites/list.tpl';?>
