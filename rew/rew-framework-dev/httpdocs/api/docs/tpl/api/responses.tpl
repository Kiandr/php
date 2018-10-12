<div class="page-header">
	<h1 id="responses">Responses</h1>
</div>
<p>
	All responses are wrapped in a JSON object. The contents of the object depend on the HTTP response code returned by the API.
</p>

<hr>
<?php include __DIR__ . '/responses/codes.tpl';?>
<hr>
<?php include __DIR__ . '/responses/success.tpl';?>
<hr>
<?php include __DIR__ . '/responses/error.tpl';?>