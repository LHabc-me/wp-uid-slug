=== UID Slug 恒链器 ===
Contributors: alfred
Tags: slug, uid, permalink
Requires at least: 6.0
Tested up to: 6.7
Stable tag: 1.0.0
License: GPLv2 or later

将 WordPress 文章（post）的 slug 自动设置为 8 位 UID，仅使用小写字母和数字，不含大写字母。

== 功能 ==
- 仅在文章“首次发布”时自动生成 UID slug
- UID 规则：8 位，仅小写字母和数字，不含大写字母
- 后续编辑不会再自动改 slug（锁定）
- 使用 wp_unique_post_slug 处理唯一性，避免冲突
- 不影响页面（page）和其他内容类型

== 注意 ==
- 建议固定链接结构使用 %postname%
- 若历史文章需批量改 UID，请单独执行批处理
