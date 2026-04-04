# WordPress 專案分析與版面調整指南 (Intro)

這份文件說明了當前 WordPress 專案的佈景主題 (Theme) 架構，以及未來若要進行「切版」(Layout/Templates 調整) 或「樣式修改」(Styling) 的標準流程。

## 1. 專案 Theme 架構解析

從專案目錄結構分析來看，網站的佈景主題放置於標準的 `wp-content/themes/` 目錄下。
目前主要的兩個重要目錄為：

*   **`newsup` (父主題 - Parent Theme)**：
    這是核心的底層主題，包含了完整的系統架構、大部分的 PHP 模板檔案（如 `header.php`, `footer.php`, `index.php`）以及核心功能。**請勿直接修改此目錄下的檔案**，因為當主題升級更新時，所有直接做的修改都會被覆蓋遺失。
*   **`newstwenty` (子主題 - Child Theme)**：
    這是網站實際啟用的主題，繼承了 `newsup` 的所有功能。
    所有的「客製化切版」、「CSS 樣式調整」與「PHP 邏輯修改」都必須強制寫在這個資料夾內。

## 2. 如何進行切版 (Layout 調整)

如果你需要調整 HTML 結構或新增區塊 (切版)，請遵循「**子主題覆蓋機制 (Template Hierarchy)**」：

1.  **尋找目標檔案**：
    先在父主題 `wp-content/themes/newsup/` 中找到負責輸出該區塊的 PHP 檔案（例如：如果你想改網站頭部，就是 `header.php`；若是文章列表則可能是 `index.php` 或 `content.php`；若是單篇文章則是 `single.php`）。
2.  **複製到子主題**：
    將該 PHP 檔案原封不動地複製到子主題目錄 `wp-content/themes/newstwenty/` 中。
    *(注意：檔案的相對路徑與名稱必須完全一致。例如若父主題是在 `template-parts/content.php`，請在子主題也建立 `template-parts/` 資料夾並放入 `content.php`)*。
3.  **修改子主題中的檔案**：
    針對子主題內的那個拷貝檔案進行 HTML/PHP 結構的修改。WordPress 會自動優先讀取子主題中的檔案，從而安全地覆蓋原有的版面。

> **例外情況**：目前 `newstwenty` 子主題裡面已經有覆寫了某些檔案（例如 `header.php`, `font.php`, `frontpage-options.php`）。若你要修改這些已經存在於子主題的檔案，可直接進行編輯。

## 3. 如何調整樣式 (CSS 修改)

*   **實體 CSS 檔案修改**：
    你需要添加或覆蓋的 CSS 樣式，請直接寫入 `wp-content/themes/newstwenty/style.css` 中。
    這個檔案會在父主題的 CSS 載入後被讀取，所以只要權重 (CSS Specificity) 足夠，就能成功覆蓋原本的樣式。
*   **WordPress 後台自訂工具 (Customizer)**：
    對於一些簡單的顏色、間距或小範圍 CSS，也可以登入 WordPress 後台，前往 **「外觀」>「自訂」>「附加的 CSS」(Appearance > Customize > Additional CSS)** 裡面新增快速樣式。

## 4. 如何新增客製化功能 (PHP 修改)

如果在切版過程中，你需要註冊新的選單位置、引入新的 JavaScript / CSS 檔案或是添加 WordPress Hooks（Action/Filter）：

*   請編輯 **`wp-content/themes/newstwenty/functions.php`**。
*   這個檔案會與父主題的 `functions.php` 一起執行，讓你可以在不破壞父主題核心功能的前提下，擴展網站功能。

## 5. 關於修改後台 (wp-admin)

是的，修改後台也**同樣在這個專案內進行**，但請絕對**不要修改**位於根目錄的 `wp-admin` 目錄內的核心檔案。 WordPress 有著嚴格的掛載與擴充機制：

*   **客製化後台頁面或功能**：可以透過子主題的 `wp-content/themes/newstwenty/functions.php` 檔案，使用如 `admin_menu` 等 Hook 來新增後台選單與自訂頁面。
*   **更改後台樣式 (CSS) 或載入自訂 JS**：一樣在子主題的 `functions.php` 中使用 `admin_enqueue_scripts` 這個 Hook，來掛載你在子主題中寫好的 CSS 或 JS 檔案，從而改變後台的視覺外觀或增加互動功能。
*   **開發外掛 (Plugins)**：如果你修改的後台功能非常龐大（且不應與「視覺排版」綁定在一起），你可以考慮在 `wp-content/plugins/` 底下創建自己的外掛專案資料夾進行開發。

## 總結工作流程建議：

1.  **要改前台 HTML 架構？** $\rightarrow$ 去 `newsup` 找檔案複製到 `newstwenty`，然後修改 `newstwenty` 裡面的檔案。
2.  **要改排版樣式？** $\rightarrow$ 直接修改 `newstwenty/style.css`。
3.  **要新增前端與後台的擴充邏輯？** $\rightarrow$ 直接編寫 `newstwenty/functions.php`。
