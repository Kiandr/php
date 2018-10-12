<?php if (!empty($history)) { ?>
    <div class="uk-container uk-container-center uk-margin-large-top uk-margin-large-bottom">
        <div id="listing-history">


            <h3>Price Change History for <?= Format::htmlspecialchars($listing['Address']); ?>, <?= Format::htmlspecialchars($listing['AddressCity']); ?>, <?= Format::htmlspecialchars($listing['AddressState']); ?> (<?= Lang::write('MLS_NUMBER'); ?><?= Format::htmlspecialchars($listing['ListingMLS']); ?>)</h3>

            <div class="uk-overflow-container">
                <table class="uk-table uk-table-hover uk-table-striped">

                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Details</th>
                        <th>Change</th>
                    </tr>
                    </thead>

                    <?php $total = count($history); ?>
                    <?php $chunks = array_chunk($history, 5); ?>
                    <?php foreach ($chunks as $i => $history) { ?>

                        <tbody <?= ($i !== 0) ? 'class="js-history-extra uk-hidden"' : ''; ?>>
                        <?php foreach ($history as $j => $change) { ?>
                            <tr>
                                <td>
                                    <span class="uk-text-bold"><time datetime="<?= date('c', $change['Date']); ?>"
                                                                     title="<?= date('D\. M. jS\, Y', $change['Date']); ?>"><?= date('m/d/Y', $change['Date']); ?></time></span>
                                </td>
                                <td><?= $change['Details']; ?></td>

                                <?php if ($change['Type'] === 'Price') { ?>
                                    <td>
                                    <span class="<?= ($change['Diff'] < 0 ? 'uk-text-warning' : 'uk-text-success'); ?>">
                                    <i class="uk-icon uk-icon-arrow-<?= ($change['Diff'] < 0 ? 'down' : 'up'); ?>"></i>
                                        <?php $multi = $change['Diff'] < 0 ? -1 : 1; ?>
                                        $<?= Format::number($change['Diff'] * $multi); ?>
                                        <?php if (!empty($change['Old'])) { ?>
                                            (<?= Format::number(($change['Diff'] / $change['Old']) * 100 * $multi, 2); ?>%)
                                        <?php } ?>
                                    </span>
                                    </td>
                                <?php } else { ?>
                                    <td>&ndash;</td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                        <?php if ($i === 0 && count($chunks) > 1) { ?>
                            <tr>
                                <td colspan="4"><a data-uk-toggle="{target: '.js-history-extra'}">Show More (<?= Format::number($total - count($chunks[0])); ?>)</a></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    <?php } ?>
                </table>
            </div>


        </div>
    </div>
<?php } ?>
