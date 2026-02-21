<?php
/**
 * Plugin Name: UID Slug 恒链器
 * Description: 自动将文章（post）的 slug 设置为 8 位 UID（仅小写字母和数字，不含大写字母），并保证唯一。仅在“首次发布”时锁定，后续编辑不再改。
 * Version: 1.0.0
 * Author: Alfred
 * License: GPL-2.0+
 */

if (!defined('ABSPATH')) {
    exit;
}

function uid_slug_is_uid($slug) {
    return (bool) preg_match('/^[a-z0-9]{8}$/', (string) $slug);
}

function uid_slug_random_strict($length = 8) {
    $pool = 'abcdefghijklmnopqrstuvwxyz0123456789';

    if ($length < 1) {
        $length = 1;
    }

    $chars = [];
    for ($i = 0; $i < $length; $i++) {
        $chars[] = $pool[random_int(0, strlen($pool) - 1)];
    }

    return implode('', $chars);
}

function uid_slug_generate_unique($post_id = 0, $post_status = 'publish', $post_type = 'post', $post_parent = 0) {
    for ($i = 0; $i < 50; $i++) {
        $candidate = uid_slug_random_strict(8);
        $unique = wp_unique_post_slug($candidate, $post_id, $post_status, $post_type, $post_parent);
        if ($unique === $candidate && uid_slug_is_uid($candidate)) {
            return $candidate;
        }
    }

    // 回退：增加长度后再交给 WP 去重
    $fallback = uid_slug_random_strict(10);
    return wp_unique_post_slug($fallback, $post_id, $post_status, $post_type, $post_parent);
}

/**
 * 仅在首次发布时生成 UID slug：
 * - 从非 publish -> publish 时触发
 * - 只处理 post 类型
 * - 若已有合规 UID 则保持不变
 */
function uid_slug_lock_on_first_publish($new_status, $old_status, $post) {
    if (!$post || !isset($post->ID)) {
        return;
    }

    if ($post->post_type !== 'post') {
        return;
    }

    if (!($old_status !== 'publish' && $new_status === 'publish')) {
        return;
    }

    $post_id = (int) $post->ID;
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }

    $current_slug = (string) get_post_field('post_name', $post_id);
    if (uid_slug_is_uid($current_slug)) {
        return;
    }

    $uid = uid_slug_generate_unique($post_id, 'publish', 'post', (int) $post->post_parent);

    remove_action('transition_post_status', 'uid_slug_lock_on_first_publish', 20);
    wp_update_post([
        'ID' => $post_id,
        'post_name' => $uid,
    ]);
    add_action('transition_post_status', 'uid_slug_lock_on_first_publish', 20, 3);
}
add_action('transition_post_status', 'uid_slug_lock_on_first_publish', 20, 3);
