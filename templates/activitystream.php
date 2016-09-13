<?php

function activityForeach($stream, $what = 0) {
    $parent = '';
    $prev_date = '';
    foreach ($stream as $st) {
        $nameDay = nameDay($st['time']);
        if($nameDay != $prev_date) {
            ?>
            <div class="activity-day">
                <?= $nameDay ?>
            </div>
        <?php } ?>
        <div class="activity-item">
            <img src="<?= base_url(returnImageUrl($st['image'])) ?>" alt="profile image" class="<?= $st['fullname'] == $parent ? 'invisible' : '' ?>">
            <div class="info">
                <div class="event">
                    <?php if($what === 0 || ($what === 2 && $st['is_ticket'] == 1)) {//TICKETS IS 0 ?>
                        <a href="<?= base_url('profile/' . $st['username']) ?>"><?= $st['fullname'] ?></a> <?= $st['event'] ?> <a href="<?= base_url('tickets/' . $st['p_name'] . '/view/' . $st['p_abbr'] . '-' . $st['ticket_id']) ?>"><?= $st['subject'] ?></a>
                        <?php if($st['text'] != null) { ?>
                            <div class="activity-content">
                                <?= $st['text'] ?>
                            </div>
                        <?php } ?>
                    <?php } elseif($what === 1 || ($what === 2 && $st['is_ticket'] == 0)) {//WIKI IS 1 ?>
                        <a href="<?= base_url('profile/' . $st['username']) ?>"><?= $st['fullname'] ?></a> <?= $st['event'] ?> <a href="<?= base_url('wiki/' . $st['p_name'] . '/display/' . $st['space_key'] . '?viewPageId=' . $st['page_id']) ?>"><?= $st['page_title'] ?></a>
                        <?php if($st['content'] != null) { ?>
                            <div class="activity-content">
                                <?= $st['content'] ?>
                            </div>
                            <a href="<?= base_url('wiki/' . $st['p_name'] . '/display/' . $st['space_key'] . '?viewPageId=' . $st['wiki_p_id'] . '&history=' . $st['update_id']) ?>">View Change</a>
                        <?php } ?>
                    <?php } ?>
                </div>
                <span>Event: <?= date(ACTIVITY_DATE_TYPE, $st['time']) ?></span>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php
        $prev_date = $nameDay;
        $parent = $st['fullname'];
    }
}
?>