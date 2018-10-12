<div class="page-header">
    <h1 id="notes">Notes</h1>
</div>

<p>
    Notes are owned by <a href="#lead">Lead</a> objects and represent lead details.
    The API allows you to create notes associated with a Lead.
</p>

<div class="docs-object">
    <div class="object-schema">
        <span class="title" id="note"><a href="#note">Notes</a> object</span>
        <table class="table table-fields">
            <tbody>
            <tr>
                <th>note</th>
                <td>
                    <p><strong>string</strong></p>
                    <p>Details about the lead.</p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="highlight">
        <span class="title">JSON Example</span>
        <pre class="prettyprint">
    {
        "note" : 'This is a note.'
    }
        </pre>
    </div>
</div>

<hr>
<?php include __DIR__ . '/notes/create.tpl';?>
