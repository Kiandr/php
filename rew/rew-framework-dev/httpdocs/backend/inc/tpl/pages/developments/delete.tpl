<form method="post" onsubmit="return confirm('Are you sure you want to delete this development?');">
    <input type="hidden" name="delete" value="1">


        <div class="bar">
            <div class="bar__title">Confirm Deletion of <?=Format::htmlspecialchars($development['title']); ?></div>
        </div>

        <div class="block">

            <div>
                <div class="article">
                    <div class="article__body">
                        <div class="article__thumb thumb thumb--medium">
                            <?php if (!empty($development['image'])) { ?>
                            <img src="/thumbs/60x60/uploads/<?=$development['image']; ?>" alt="">
                            <?php } else { ?>
                            <img src="/thumbs/60x60/uploads/listings/na.png" alt="">
                            <?php }?>
                        </div>
                        <div class="article__content">
                            <a class="text text--strong" href="edit/?id=<?=$development['id']; ?>"><?=Format::htmlspecialchars($development['title']); ?></a>
                            <div class="text text--mute" style="text-overflow: ellipsis;"><?=Format::htmlspecialchars(Format::truncate($development['subtitle'], 150)); ?> <?=Format::htmlspecialchars(Format::truncate($development['description'], 150)); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <h2>Are you sure you want to delete this development?</h2>

            <div class="btns">
                <a href="../.." class="btn">Cancel</a>
                <button type="submit" class="btn btn--negative">Confirm Delete</button>
            </div>
        </div>


</form>