# WordPress：將硬編碼 CSS 值抽出為 Customizer 後台設定

## 使用時機

當 WordPress child theme 的 `style.css` 有硬編碼的數值（px、行數、顏色等），
希望讓管理者可以從後台（外觀 → 自訂）直接調整時，套用此流程。

---

## 核心原則

**唯一可靠的動態 CSS 注入方式是 `wp_add_inline_style`**，不要用 `wp_head`。

原因：
- `wp_head` 注入的 `<style>` 雖然在 `<link>` 之後，但 WordPress 不保證時機穩定
- `wp_add_inline_style('handle', $css)` 保證輸出緊接在指定 stylesheet `<link>` 正下方
- 兩個 `!important` 相同 specificity 時，source order 靠後者獲勝；inline style 在 link 之後 = 永遠覆寫

**不要用 `flex` shorthand 搭配 CSS variables**：
- `flex: 0 0 var(--x)` 在部分瀏覽器解析不穩定
- 改用 longhand：`flex-grow`, `flex-shrink`, `flex-basis` 分開寫

---

## 四步驟流程

### Step 1 — `style.css`：設定靜態預設值

把原本的硬編碼值保留為「預設值」，動態 CSS 之後會覆寫它。
規則上加 `!important` 確保靜態值有效（動態 CSS 用相同 specificity + 同樣 `!important` 覆寫）。

```css
/* 預設值，後台動態 CSS 覆寫 */
.your-selector .target {
    height: 300px !important;
    max-height: 300px !important;
}
```

### Step 2 — `frontpage-options.php`：新增 Customizer 控制項

```php
// Setting
$wp_customize->add_setting('your_setting_key', array(
    'default'           => 300,          // 與 style.css 預設值一致
    'capability'        => 'edit_theme_options',
    'sanitize_callback' => 'absint',     // 數字用 absint，文字用 sanitize_text_field
));

// Control
$wp_customize->add_control('your_setting_key', array(
    'type'        => 'number',
    'label'       => esc_html__('設定標籤', 'textdomain'),
    'section'     => 'target_section_id',  // 放在哪個 section
    'settings'    => 'your_setting_key',
    'input_attrs' => array('min' => 50, 'max' => 800, 'step' => 10),
));
```

常用 section ID：
- `frontpage_main_banner_section_settings` — Slide Banner 區塊
- `post_image_options` — Post Image Settings 區塊

### Step 3 — `functions.php`：在 `wp_enqueue_styles` 讀值並輸出動態 CSS

在已有的 `newstwenty_enqueue_styles()` 函式末尾，`wp_enqueue_style` 之後加入：

```php
$value = max(1, absint(get_theme_mod('your_setting_key', 300)));

$dynamic_css = "
    .your-selector .target {
        height: {$value}px !important;
        max-height: {$value}px !important;
    }
";
wp_add_inline_style('newstwenty-style', $dynamic_css);
```

注意事項：
- `max(1, absint(...))` 防止 0 或負數
- `wp_add_inline_style` 的 handle 必須已經被 `wp_enqueue_style` 註冊（`newstwenty-style`）
- 所有動態規則集中在**同一個** `wp_add_inline_style` 呼叫，避免多次呼叫

### Step 4 — 驗證

1. 後台儲存後，在前端「檢視原始碼」搜尋 `newstwenty-style-inline-css`
2. 確認裡面的 px 值已反映後台設定
3. 確認該規則出現在 `<link rel="stylesheet" id="newstwenty-style-css">` 之後

---

## 本專案已抽出的設定對照表

| 後台 Section | Setting Key | 預設值 | 對應 CSS |
|---|---|---|---|
| Slide Banner | `banner_image_max_width` | 480px | `.mg-fea-area ... .col-12.col-md-6:has(.back-img)` flex-basis / max-width |
| Slide Banner | `banner_image_max_height` | 480px | `.mg-fea-area ... .mg-post-thumb.back-img` height / max-height |
| Slide Banner | `banner_excerpt_height` | 80px | `.mg-fea-area ... .mg-content` max-height |
| Post Image Settings | `article_image_max_width` | 300px | `.mg-posts-modul-6 ... .col-12.col-md-6:has(.back-img)` flex-basis / max-width |
| Post Image Settings | `article_image_max_height` | 300px | `.mg-posts-modul-6 ... .mg-post-thumb.back-img` height / max-height |
| Post Image Settings | `article_excerpt_words` | 100字元 | PHP `newsup_the_excerpt` 截字（中���為字元數） |

---

## 陷阱備忘

| 錯誤做法 | 正確做法 |
|---|---|
| `add_action('wp_head', ...)` 注入 `<style>` | `wp_add_inline_style('newstwenty-style', $css)` |
| `flex: 0 0 var(--x) !important` | `flex-basis: {$value}px !important`（PHP 直接插值）|
| `style.css` 保留 `!important` 同時 `wp_head` 也加 `!important` | 只用 `wp_add_inline_style`，source order 保證覆寫 |
| CSS variables 搭配 `-webkit-line-clamp` | PHP 直接輸出數字到 CSS rule |
| 以「行數」控制摘要顯示量 | 以「高度(px)」控制，`max-height + overflow: hidden` 更直觀可控 |
