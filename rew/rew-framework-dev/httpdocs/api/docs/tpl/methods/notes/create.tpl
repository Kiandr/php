<h2 id="notes-create">Create a new note</h2>

<p><code>POST http://<?=$_SERVER['HTTP_HOST'];?>/api/crm/v1/leads/:email/notes</code></p>

<p>
    Creating a note will associate it with the given <a href="#lead">Lead</a>.
</p>

<p>
    The <a href="#responses">response</a> of this request contains the <a href="#note">Note</a> object that was created.
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
                        The e-mail address of the lead for which you are creating a saved search.
                    </p>
                </td>
            </tr>
            <tr>
                <th>note</th>
                <td>
                    <p><strong>string</strong></p>
                    <p>
                        The Note's details.
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="highlight">
        <span class="title">Example Request</span>
        <pre class="prettyprint">POST /api/crm/v1/leads/andy@realestatewebmasters.com/notes HTTP/1.1
Host: <?=$_SERVER['HTTP_HOST'] . PHP_EOL;?>
User-Agent: myAPIClient/1.0
X-REW-API-Key: <?=$api_key . PHP_EOL;?>

<?= http_build_query(array('note' => 'This is a note.'));?>
</pre>
    </div>
    <div class="highlight">
        <span class="title">Example Response</span>
        <pre class="prettyprint">HTTP/1.1 204 No Content</pre>
    </div>
</div>