<h2 id="responses-success">Success <small><span class="label label-success">2XX</span></small></h2>
<p>
	Responses with HTTP codes in the 2xx range indicate success. For these responses, the JSON object will contain a single <code>data</code> object
	that consists of the payload returned by the resource. An example of a successful response is below:
</p>

<strong>Example Response</strong>

<div class="highlight">
<pre class="prettyprint">HTTP/1.1 200 OK
Content-Type: application/json

{
    "data" : [
        {
            "id" : 1,
            "name" : "John Smith",
            "email" : "john.smith@example.com",
            "title" : "Broker / Owner"
        },
        {
            "id" : 2,
            "name" : "Jane Smith",
            "email" : "jane.smith@example.com",
            "title" : "REALTOR&reg;"
        }
    ]
}</pre>
</div>