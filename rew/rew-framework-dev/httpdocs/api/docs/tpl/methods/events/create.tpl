<h2 id="events-create">Create a new event</h2>

<p><code>POST http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/leads/:email/events</code></p>

<p>
	Creating an event will add it to the Lead Management system and associate it with the specified lead.
	Once created, it will appear in the Lead Manager under that lead's History tab.
</p>

<p>
	The <a href="#responses">response</a> of this request contains the <a href="#event">Event</a> object that was created.
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
							The e-mail address of the lead for which you are creating an event.
						</p>
					</td>
				</tr>
				<tr>
					<th>type</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The event's type.
						</p>
						<p>
							<small>Accepted values: <code>Action</code> or <code>Phone</code></small>
						</p>
					</td>
				</tr>
				<tr>
					<th>subtype</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The event's sub-type.
						</p>
						<p>
							<small><code>Action</code> Accepted values: <code>FormSubmission</code></small>
                        </p>
                        <p>
                            <small><code>Phone</code> Accepted values: <code>Attempt</code>, <code>Contact</code>, <code>Invalid</code>, <code>Voicemail</code></small>
						</p>
					</td>
				</tr>
				<tr>
					<th>details</th>
					<td>
						<p><strong>object</strong></p>
						<p>
							The metadata associated with the event. The data required for this argument differs depending on the type & sub-type of the event.
						</p>
						<p>Required details fields are outlined below on a per-type basis.</p>

						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Type</th>
									<th>Sub-Type</th>
									<th>Required details fields</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Action</td>
									<td>FormSubmission</td>
									<td>
										<table class="table">
											<tbody>
												<tr>
													<th>page</th>
													<td>
														<p><strong>string</strong></p>
														<p>
															Absolute URL of the page where the submission occurred.
														</p>
													</td>
												</tr>
												<tr>
													<th>form</th>
													<td>
														<p><strong>string</strong></p>
														<p>
															Name of the form that was submitted.
														</p>
													</td>
												</tr>
												<tr>
													<th>data</th>
													<td>
														<p><strong>object</strong></p>
														<p>
															An object containing the form fields as the keys and their submitted values.
														</p>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
                                <tr>
                                    <td>Phone</td>
                                    <td>Attempt, Contact, Invalid, Voicemail</td>
                                    <td>
                                        <table class="table">
                                            <tbody>
                                            <tr>
                                                <th>details</th>
                                                <td>
                                                    <p><strong>string</strong></p>
                                                    <p>
                                                        Information related to the type of phone call made.
                                                    </p>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="highlight">
		<span class="title">Example Request</span>
<pre class="prettyprint">POST /api/crm/v1/leads/andy@realestatewebmasters.com/events HTTP/1.1
Host: <?=$_SERVER['HTTP_HOST'] . PHP_EOL;?>
User-Agent: myAPIClient/1.0
X-REW-API-Key: <?=$api_key . PHP_EOL;?>

<?=http_build_query(array(
	'type' => 'Action',
	'subtype' => 'FormSubmission',
	'details' => array(
		'page' => 'http://' . $_SERVER['HTTP_HOST'] . '/contact.php',
		'form' => 'Contact Form',
		'data' => array(
			'contactform' => 'true',
			'email' => '',
			'first_name' => '',
			'last_name' => '',
			'onc5khko' => 'REW',
			'sk5tyelo' => 'Andy',
			'mi0moecs' => 'andy@realestatewebmasters.com',
			'telephone' => '',
			'subject' => 'Testing',
			'comments' => 'This is a test.',
		),
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
    }
}</pre>
	</div>
</div>