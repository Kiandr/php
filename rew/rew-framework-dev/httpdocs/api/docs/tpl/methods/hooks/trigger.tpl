<h2 id="hooks-trigger">Trigger hook</h2>

<p><code>POST http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/hooks</code></p>

<p>
	The <a href="#responses">response</a> of this request is empty and returns <code>204: No Content</code> on success.
</p>

<div class="docs-object">
	<div class="object-schema">
		<span class="title">Request Arguments</span>
		<table class="table table-fields">
			<tbody>
				<tr>
					<th>hook</th>
					<td>
						<p><strong>string</strong></p>
						<p>
							The hook's name
						</p>
						<p>
							<small>Accepted values:
								<code><?=Hooks::HOOK_LEAD_TEXT_INCOMING; ?></code>
							</small>
						</p>
					</td>
				</tr>
				<tr>
					<th>data</th>
					<td>
						<p><strong>object</strong></p>
						<p>
							The metadata associated with the hook.
							The data required for this argument differs depending on the hook being invoked.
							Required fields are outlined below on a per-hook basis.
						</p>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Hook</th>
									<th>Required data</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><code><?=Hooks::HOOK_LEAD_TEXT_INCOMING; ?></code></td>
									<td>
										<table class="table">
											<tbody>
												<tr>
													<th>to</th>
													<td>
														<p><strong>string</strong></p>
														<p>
															Phone # that received SMS
														</p>
													</td>
												</tr>
												<tr>
													<th>from</th>
													<td>
														<p><strong>string</strong></p>
														<p>
															Phone # that sent the SMS
														</p>
													</td>
												</tr>
												<tr>
													<th>media</th>
													<td>
														<p><strong>array|string</strong></p>
														<p>
															Attached media URLs
														</p>
													</td>
												</tr>
												<tr>
													<th>message</th>
													<td>
														<p><strong>string</strong></p>
														<p>
															Text message's body
														</p>
													</td>
												</tr>
												<tr>
													<th>request</th>
													<td>
														<p><strong>object</strong></p>
														<p>
															Raw request data from Twilio<br>
															<a href="https://www.twilio.com/docs/api/twiml/sms/twilio_request">https://www.twilio.com/docs/api/twiml/sms/twilio_request</a>
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
<pre class="prettyprint">POST /api/crm/v1/hooks HTTP/1.1
Host: <?=$_SERVER['HTTP_HOST'] . PHP_EOL;?>
User-Agent: myAPIClient/1.0
X-REW-API-Key: <?=$api_key . PHP_EOL;?>

<?=http_build_query(array(
	'hook' => Hooks::HOOK_LEAD_TEXT_INCOMING,
	'data' => array(
		'to'		=> 1234567890,
		'from'		=> 1234567890,
		'message'	=> 'Hello world!',
		'media'		=> array('http://www.nyan.cat/cats/nyancat.gif')
	),
));?>
</pre>
	</div>
	<div class="highlight">
		<span class="title">Example Response</span>
		<pre class="prettyprint">HTTP/1.1 204 No Content</pre>
	</div>
</div>