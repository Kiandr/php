### Using the "Image Left", "Image Right", "Video Left", "Video Right" functionality in tinyMCE

- Content (except for lists) entered/inserted within the tinyMCE editor is placed inside a `<p>` tag.
- The option adds a CSS class to that `<p>` tag to enable it to display as intended.
- It is important to know where the cursor is located as you type or edit content.
- After inserting an image/video hit the left arrow key to see the cursor, then press `enter` to create a new line (`<p>` tag) and add your content.
- If one of the options has been applied to your image/video you will have to hit `enter` a second time to create a `<p>` tag that clears the floated content.
- To clear the floated content your cursor should be after the last letter of the text you entered in the first `<p>` tag (new line above).
- Any content that is entered below the floated content (inside the second `<p>` tag) should display normally.

#### Things to Note

- It is important to ensure you add all the needed content after the image before formatting the image. Otherwise, it’d be difficult to align the content to the right.
- If content is wider than the allocated space beside the floated image, then the content will show below the image.