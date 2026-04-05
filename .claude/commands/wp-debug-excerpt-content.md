# WordPress：診斷並修復文章摘要顯示不完整

## 使用時機

前台文章摘要文字被截斷、顯示不完整，或調整後台設定後沒有任何變化。

---

## 診斷流程：先找出截斷發生在哪一層

截斷可能發生在三個不同的地方，必須由外而內逐層確認：

```
資料庫 → PHP 截字 → CSS 裁切 → 瀏覽器顯示
```

### Step 1 — 確認資料庫裡有沒有完整文字

建立臨時偵錯頁面 `debug-post-content.php` 放在網站根目錄，用瀏覽器開啟：

```php
<?php
define('SHORTINIT', true);
require_once __DIR__ . '/wp-load.php';
global $wpdb;
$posts = $wpdb->get_results("
    SELECT ID, post_title,
           LENGTH(post_content) as clen, post_content,
           LENGTH(post_excerpt) as elen, post_excerpt
    FROM wp_posts WHERE post_status='publish' AND post_type='post'
    ORDER BY ID DESC LIMIT 3
");
header('Content-Type: text/plain; charset=utf-8');
foreach ($posts as $p) {
    echo "ID:{$p->ID} | {$p->post_title}\n";
    echo "content(len={$p->clen}): {$p->post_content}\n\n";
    echo "excerpt(len={$p->elen}): {$p->post_excerpt}\n\n";
    $metas = $wpdb->get_results($wpdb->prepare(
        "SELECT meta_key, LEFT(meta_value,120) as val FROM wp_postmeta WHERE post_id=%d ORDER BY meta_key", $p->ID
    ));
    foreach ($metas as $m) echo "  [{$m->meta_key}] => {$m->val}\n";
    echo "\n";
}
```

**判斷**：
- `clen` 很小（< 200）→ 資料庫本身就只有殘缺資料，需找真正的資料來源
- `clen` 正常（上千）→ 截斷在 PHP 或 CSS 層，繼續往下查

使用完畢**立即刪除**此檔案。

### Step 2 — 確認截斷在 PHP 還是 CSS

看 HTML 原始碼：

| 現象 | 判斷 |
|------|------|
| `<p>文字…</p>` 文字很短，`…` 是 `&hellip;` | PHP `wp_trim_words` 截字 |
| `<p>` 裡有完整文字，但視覺上被切掉 | CSS `max-height` / `overflow:hidden` / `line-clamp` 裁切 |
| 文字短且結尾有 Unicode `…`（U+2026） | 原始資料本身就含省略號 |

### Step 3 — 找出是哪個 PHP 函式在截字

搜尋 `newsup_the_excerpt` 和 `mg-content` 的呼叫點：

```bash
grep -rn "newsup_the_excerpt\|mg-content" wp-content/themes/ --include="*.php"
```

**常見陷阱**：CSS class `mg-posts-modul-6` 看起來像是 list content，
但實際上可能來自 **Widget**（`widget-latest-news.php`），
而不是 `newsup_main_list_content`。這兩個是完全不同的程式碼路徑。

---

## 修復模式

### 模式 A：post_excerpt 蓋掉 post_content（API 給的摘要不完整）

父主題 `newsup_the_excerpt` 的原始邏輯：
```php
$source_content = $post_obj->post_content;
if (!empty(get_the_excerpt($post_obj))) {
    $source_content = get_the_excerpt($post_obj); // ← API excerpt 蓋掉完整 content
}
```

**修法**：在 child theme `functions.php` 最前面定義同名函式（child theme 先載入）：

```php
if (!function_exists('newsup_the_excerpt')) :
    function newsup_the_excerpt($length = 0, $post_obj = null) {
        global $post;
        if (is_null($post_obj)) $post_obj = $post;
        $length = absint($length);
        if (0 === $length) return;

        // 文章型（length >= 20）→ 使用後台設定字數
        if ($length >= 20) {
            $length = max(1, absint(get_theme_mod('article_excerpt_words', 100)));
        }
        // 優先 post_content，fallback post_excerpt
        $source_content = trim($post_obj->post_content);
        if (empty($source_content)) $source_content = $post_obj->post_excerpt;

        $source_content = preg_replace('/\s*(&nbsp;|\xA0)\s*/u', ' ', $source_content);
        $source_content = preg_replace('`\[[^\]]*\]`', '', $source_content);
        return wp_trim_words($source_content, $length, '&hellip;');
    }
endif;
```

**length 閾值設計**：
- `>= 20`：文章型（widget 的 30、list 的 30）→ 讀後台設定
- `< 20`：側欄/trending 等小元件（15）→ 保留原本短截斷

### 模式 B：需要傳入完整文字（由 CSS max-height 控制可見高度）

用於 Slider Banner：PHP 傳完整文字，CSS 決定顯示多少。

```php
function newstwenty_full_text($post_obj = null, $length = 0) {
    global $post;
    if (is_null($post_obj)) $post_obj = $post;
    $content = trim($post_obj->post_content);
    if (empty($content)) $content = $post_obj->post_excerpt;
    $content = preg_replace('/\s*(&nbsp;|\xA0)\s*/u', ' ', $content);
    $content = preg_replace('`\[[^\]]*\]`', '', $content);
    $content = wp_strip_all_tags($content);
    $content = trim($content);
    if ($length > 0) {
        $content = wp_trim_words($content, $length, '&hellip;');
    }
    return $content;
}
```

- `$length = 0` → 完整文字（Slider Banner）
- `$length > 0` → 截至指定字數（文章列表）

---

## 中文／CJK 字元計數陷阱

**`wp_trim_words` 在中文 locale 下計「字元」而非「單字」。**

```php
// WordPress 內部判斷
if (strpos(_x('words', 'Word count type.'), 'characters') === 0) {
    // CJK 模式：preg_match_all('/./u', ...) → 按字元切
}
```

| 語言 | `wp_trim_words(30)` 的實際效果 |
|------|-------------------------------|
| 英文 | 約 30 個英文單字（~150 字元） |
| 中文 | 30 個中文字元（不到一句話）   |

**後台設定建議預設值**：
- 短摘要（widget）：100 字元 ≈ 3–4 句話
- 中等摘要：150–200 字元

---

## Widget 無法從外部控制長度的處理方式

Widget 通常硬編碼長度（如 `newsup_the_excerpt(30)`），無法透過參數傳入。
解法優先順序：

1. **覆寫 `newsup_the_excerpt`**（最簡單）：在函式內讀取 theme mod，讓 widget 的硬編碼值失效
2. 取消註冊 + 重新註冊 widget 類別（複雜，需依賴父主題的 base class）
3. 使用 `widget_text` 等 filter hook（只適用特定 widget 類型）

---

## 本專案的函式呼叫路徑備忘

| 區塊 | 呼叫路徑 | 截字方式 |
|------|---------|---------|
| Slider Banner | `hooks.php` → `newstwenty_full_text($post)` | CSS `max-height`（後台 px） |
| 首頁文章 Widget | `widget-latest-news.php` → `newsup_the_excerpt(30)` | PHP 字元數（後台設定覆寫） |
| 首頁文章 List | `hooks.php` 覆寫 → `newstwenty_full_text($post, $words)` | PHP 字元數（後台設定） |
| Trending Posts | `hooks.php` → `newsup_the_excerpt(15)` | 保留 15 字元硬編碼 |
