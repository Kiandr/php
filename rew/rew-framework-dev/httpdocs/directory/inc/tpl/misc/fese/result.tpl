<article class="col w1/3 w1/2-md w1/1-sm directory-listing<?=($entry['featured'] == 'Y') ? ' featured' : ''; ?>">
    <div class="body">
        <div class="photo"><a href="<?=$entry['url_details']; ?>" class="business-thumb"><img src="/thumbs/190x100/<?=$entry['image']; ?>" alt=""></a></div>
        <div class="details">
            <h4 class="name"><a href="<?=$entry['url_details']; ?>"><?=htmlspecialchars($entry['business_name']); ?></a></h4>
            <?php if (!empty($entry['phone'])) { ?>
                <p class="val phone"><?=htmlspecialchars($entry['phone']); ?></p>
            <?php } ?>
            <?php if (!empty($entry['address'])) { ?>
                <p class="val address"><?=htmlspecialchars($entry['address']); ?></p>
            <?php } ?>
            <?php if (!empty($entry['description'])) { ?>
                <p class="description"><?=Format::truncate(strip_tags($entry['description']), 125, '&hellip;', false); ?></p>
            <?php } ?>
            <a class="btn strong" href="<?=$entry['url_details']; ?>">View Details <i class="icon-chevron-right"></i></a>
        </div>
    </div>
</article>