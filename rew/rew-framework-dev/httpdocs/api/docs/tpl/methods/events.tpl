<div class="page-header">
	<h1 id="events">History Events</h1>
</div>

<p>
	Event objects represent tracked activity or actions that are associated with a <a href="#lead">Lead</a>, <a href="#agent">Agent</a>, or both.
	Some examples of history events include form submissions, viewed listings, sent e-mails, and more. The API allows you to create history events
	and associate them with a lead.
</p>

<div class="docs-object">
	<div class="object-schema">
		<span class="title" id="event"><a href="#event">Event</a> object</span>
		<table class="table table-fields">
			<tbody>
				<tr>
					<th>type</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The event's main type.
						</p>
						<p><small>Possible Values: <code>Action</code></small></p>
					</td>
				</tr>
				<tr>
					<th>subtype</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The event's sub-type.
						</p>
						<p><small>Possible Values: <code>FormSubmission</code></small></p>
					</td>
				</tr>
				<tr>
					<th>details</th>
					<td>
						<p><strong>object</strong></p>
						<p>
							Metadata associated with the event. This differs depending on the type & sub-type of the event.
						</p>
					</td>
				</tr>
				<tr>
					<th>timestamp</th>
					<td>
						<p><strong>integer</strong></p>
						<p>A UNIX timestamp (in UTC) of when the event was created.</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">JSON Example</span>
<pre class="prettyprint">{
    "type" : "Action",
    "subtype" : "FormSubmission",
    "details" : {
        "form" : "Contact Form",
        "page" : "http://<?=$_SERVER['HTTP_HOST'];?>/contact.php",
        "data" : {
            "contactform" : "true",
            "email" : "",
            "first_name" : "",
            "last_name" : "",
            "onc5khko" : "REW",
            "sk5tyelo" : "Andy",
            "mi0moecs" : "andy@realestatewebmasters.com",
            "telephone" : "",
            "subject" : "Testing",
            "comments" : "This is a test."
        }
    },
    "timestamp" : <?=time() . PHP_EOL;?>
}</pre>
	</div>
</div>

<hr>
<?php include __DIR__ . '/events/create.tpl';?>