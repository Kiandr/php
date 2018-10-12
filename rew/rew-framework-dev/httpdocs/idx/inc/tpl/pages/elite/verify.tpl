<?php if (!empty($show_form)) { ?>
    <div class="modal-body">
        <?php include $page->locateTemplate('idx', 'misc', 'messages'); ?>
        <?php if (empty($success)) { ?>
            <p>It looks like you have almost completed your registration, but you haven't clicked on the confirmation link we sent to your email address. You simply need to click on that link in the email, or paste the code from that email into this space below:</p>
        <?php } ?>

        <form action="?submit" method="post">

            <input type="hidden" name="step" value="verify">

            <div class="uk-form-row">
                <input type="text" name="code" value="<?= Format::htmlspecialchars($_POST['code']); ?>" placeholder="Verification Code" class="uk-form-width-large uk-form-large" required>
            </div>

            <div class="uk-form-row">
                <button class="uk-button" type="submit">Verify Email</button>
            </div>

        </form>

        <p>Need us to re-send the email to you? Use the form below to have it sent to you.<br>(The first email may have gone into your junk folder by mistake). If you have misspelled your email address, please <a href="/contact.php" target="_blank">reach out to us</a> to have it adjusted.</p>


        <form class="uk-form" id="resend-code" action="?submit" method="post">

            <input type="hidden" name="step" value="email">

            <div class="uk-form-row">
                <input type="email" name="email" value="<?= Format::htmlspecialchars($_POST['email']); ?>" placeholder="Email Address" class="uk-form-width-large uk-form-large" disabled>
            </div>

            <div class="uk-form-row">
                <label><input type="checkbox" name="resend" value="1" required class="uk-margin-right"> Yes, please re-send my confirmation link. <em class="required">*</em></label>
            </div>

            <div class="uk-form-row">
                <button class="uk-button" type="submit">Send Code</button>
            </div>

        </form>

        <p>Thanks for your understanding as we work to give you the best possible experience!</p>
    </div>

<?php } else { ?>

    <div class="modal-header">
        <h1>Thank you!</h1>
    </div>

    <div class="modal-body">
        <p>Your email has successfully been verified! You can now save listings, save searches and more!</p>
        <p><a href="<?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX']); ?>" target="_parent">Click here to start searching</a></p>

    <?php
        // Trigger Save Listing
        if (!empty($_SESSION['bookmarkListing'])) { ?>
            <script>IDX.Favorite({'mls':'<?= $_SESSION['bookmarkListing']; ?>','force':true,'feed':'<?= $_SESSION['bookmarkFeed']; ?>'});</script>
            <?php unset($_SESSION['bookmarkListing'], $_SESSION['bookmarkFeed']);
        }
        // Trigger Save Search
        if (!empty($_SESSION['saveSearch'])) { ?>
            <script>IDX.SaveSearch(<?= $_SESSION['saveSearch']; ?>);</script>
            <?php unset($_SESSION['saveSearch']);
        } ?>
    </div>
<?php } ?>
