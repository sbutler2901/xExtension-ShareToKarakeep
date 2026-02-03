<?php
declare(strict_types=1);

namespace ShareToKarakeep\Models;

class View extends \Minz_View {
    public ?\FreshRSS_Entry $entry = null;
    public string $rss_url = '';
    public string $rss_title = '';
}
