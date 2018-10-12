<table width="650" cellspacing="0" cellpadding="0" align="center">
	<tbody>
	<tr>
		<td><a style="color: #555;" href="{wb_url}">View this email in your browser.</a></td>
	</tr>
	<tr>
		<td>
			<div style="background-color: #ffffff; padding: 10px 40px 40px; font-family: arial; font-size: 13px; line-height: 20px; color: #333333; border-bottom: 5px solid #252525;">
				<p>Hello {first_name},</p>
				<p>Your saved search, <a style="color: #777; font-size: 12px; font-style: italic;" href="{search_url}">{search_title}</a>, has new matches.</p>
				<h4 style="font-family: georgia; font-style: italic; font-size: 16px; font-weight: normal;">New Listings Matching Your Saved Search Criteria</h4>
				{results}
				<p style="background-color: #eee; padding-top: 10px; padding-bottom: 10px; text-align: center; border-top-color: #ccc; border-top-width: 2px; border-top-style: dotted; color: #333; font-family: georgia; font-style: italic;">Search Many More Properties on <a style="color: #555;" href="{url}">{domain}</a></p>
				{signature}
			</div>
		</td>
	</tr>
	<tr>
		<td style="background-color: #eee;">
            <a href="<?= $unsubscribe; ?>" title="Opens new browser window"
               style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; display: inline-block; color: <?= $style["footer_link"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 11px; line-height: 150%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 12px; padding-right: 12px; text-align: left;">Unsubscribe
                from this list.</a>

            <a href="<?= $sub_preferences; ?>" title="Opens new browser window"
               style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; display: inline-block; color: <?= $style["footer_link"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 11px; line-height: 150%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 12px; text-align: left;">Update subscription preferences</a><br>
        </td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	</tbody>
</table>