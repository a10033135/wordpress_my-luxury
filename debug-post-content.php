<?php
/**
 * 臨時偵錯：檢查文章的 post_content / post_excerpt / post_meta 實際內容
 * 使用完畢請刪除此檔案
 */
define('SHORTINIT', true);
require_once __DIR__ . '/wp-load.php';

// 關閉 SHORTINIT 模式需要的補充
if (!function_exists('wp_strip_all_tags')) {
    require_once ABSPATH . WPINC . '/formatting.php';
}

global $wpdb;

$posts = $wpdb->get_results("
    SELECT ID, post_title,
           LENGTH(post_content) as clen,
           post_content,
           LENGTH(post_excerpt) as elen,
           post_excerpt
    FROM wp_posts
    WHERE post_status = 'publish' AND post_type = 'post'
    ORDER BY ID DESC
    LIMIT 3
");

header('Content-Type: text/plain; charset=utf-8');

foreach ($posts as $p) {
    echo "========================================\n";
    echo "ID: {$p->ID}  |  {$p->post_title}\n";
    echo "post_content (len={$p->clen}):\n";
    echo $p->post_content . "\n\n";
    echo "post_excerpt (len={$p->elen}):\n";
    echo $p->post_excerpt . "\n\n";

    // 列出所有 post_meta key
    $metas = $wpdb->get_results($wpdb->prepare(
        "SELECT meta_key, LEFT(meta_value,120) as val FROM wp_postmeta WHERE post_id = %d ORDER BY meta_key",
        $p->ID
    ));
    echo "post_meta keys (" . count($metas) . "):\n";
    foreach ($metas as $m) {
        echo "  [{$m->meta_key}] => {$m->val}\n";
    }
    echo "\n";
}
