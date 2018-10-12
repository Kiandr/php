<?php  if (!empty($offices)) { ?>
    <div class="cols">
        <?php foreach ($offices as $office) { ?>
            <a href="/offices.php?oid=<?=$office['id']; ?>" class="col stk w1/3 w1/1-sm w1/2-md">
                <div>
                    <div class="img wFill h1/1 fade img--cover">
                        <img data-src="<?=$office['image']; ?>" src="<?=$office_placeholder; ?>" alt="" />
                    </div>
                </div>
                <div>
                    <div class="txtC pad">
                        <h2><?=Format::htmlspecialchars($office['title']); ?></h2>
                    </div>
                    <div class="BM txtC pad">
                        <p class="description"><?=Format::htmlspecialchars($office['location']); ?></p>
                        <?=!empty($office['phone']) ? Format::htmlspecialchars($office['phone']) : ''; ?>
                    </div>
                </div>
            </a>
        <?php } ?>
    </div>
<?php } ?>