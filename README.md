# wp-uid-slug

WordPress 插件：**UID Slug 恒链器**。

将文章（post）的 slug 在首次发布时自动锁定为 8 位 UID（仅小写字母），并保证唯一。

## 功能
- 首次发布自动生成 UID slug
- UID 规则：8 位，仅小写字母
- 后续编辑不再改写 slug
- 使用 `wp_unique_post_slug` 避免冲突

## 安装
上传本仓库中的 `wp-uid-slug.php` 与 `readme.txt` 到插件目录，或使用 Release 中的 zip 安装。

## 版本
当前版本：`1.0.0`
