<?php

/**
 * Plugin Name: bbPress - Jetpack Markdown
 * Plugin URI:  https://bbpress.org
 * Description: Use Jetpack Markdown in bbPress Forums, Topics, and Replies
 * Author:      John James Jacoby
 * Author URI:  https://jjj.blog
 * Version:     0.1.0
 * Text Domain: bbp-jp-markdown
 * Domain Path: /languages/
 * License:     GPLv2 or later (license.txt)
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Normally bbPress will try to use the "`" character to wrap text in <code> and
 * <pre> HTML tags, but with Jetpack Markdown active, things get a little weird.
 *
 * This function juggles the filters around to unhook bbPress and re-hook
 * Jetpack back on to all of the content areas bbPress provides.
 *
 * If Jetpack Markdown is not active, this plugin will leave bbPress as-is.
 *
 * @since 0.1.0
 */
function bbp_jp_markdown_init() {

	// Skip if no Jetpack
	if ( ! class_exists( 'WPCom_Markdown' ) ) {
		return;
	}

	// Add markdown support to bbPress's post types
	add_post_type_support( 'forum', 'wpcom-markdown' );
	add_post_type_support( 'topic', 'wpcom-markdown' );
	add_post_type_support( 'reply', 'wpcom-markdown' );

	// Unhook bbPress's backtick code-trick
	remove_filter( 'bbp_new_reply_pre_content',  'bbp_encode_bad',  10 );
	remove_filter( 'bbp_new_reply_pre_content',  'bbp_code_trick',  20 );
	remove_filter( 'bbp_new_reply_pre_content',  'bbp_filter_kses', 30 );
	remove_filter( 'bbp_new_topic_pre_content',  'bbp_encode_bad',  10 );
	remove_filter( 'bbp_new_topic_pre_content',  'bbp_code_trick',  20 );
	remove_filter( 'bbp_new_topic_pre_content',  'bbp_filter_kses', 30 );
	remove_filter( 'bbp_new_forum_pre_content',  'bbp_encode_bad',  10 );
	remove_filter( 'bbp_new_forum_pre_content',  'bbp_code_trick',  20 );
	remove_filter( 'bbp_new_forum_pre_content',  'bbp_filter_kses', 30 );
	remove_filter( 'bbp_edit_reply_pre_content', 'bbp_encode_bad',  10 );
	remove_filter( 'bbp_edit_reply_pre_content', 'bbp_code_trick',  20 );
	remove_filter( 'bbp_edit_reply_pre_content', 'bbp_filter_kses', 30 );
	remove_filter( 'bbp_edit_topic_pre_content', 'bbp_encode_bad',  10 );
	remove_filter( 'bbp_edit_topic_pre_content', 'bbp_code_trick',  20 );
	remove_filter( 'bbp_edit_topic_pre_content', 'bbp_filter_kses', 30 );
	remove_filter( 'bbp_edit_forum_pre_content', 'bbp_encode_bad',  10 );
	remove_filter( 'bbp_edit_forum_pre_content', 'bbp_code_trick',  20 );
	remove_filter( 'bbp_edit_forum_pre_content', 'bbp_filter_kses', 30 );

	// Unhook bbPress's backtickt code-trick reversal (for editing)
	remove_filter( 'bbp_get_form_forum_content', 'bbp_code_trick_reverse' );
	remove_filter( 'bbp_get_form_topic_content', 'bbp_code_trick_reverse' );
	remove_filter( 'bbp_get_form_reply_content', 'bbp_code_trick_reverse' );

	// Hook
	add_filter( 'bbp_get_form_forum_content', 'bbp_jp_markdown_edit', 8 );
	add_filter( 'bbp_get_form_topic_content', 'bbp_jp_markdown_edit', 8 );
	add_filter( 'bbp_get_form_reply_content', 'bbp_jp_markdown_edit', 8 );
}
add_action( 'bbp_init', 'bbp_jp_markdown_init' );

/**
 * Swap the post_content with post_content_filtered, but only if we are editing
 * a forum, topic, or reply via the front-end of the forums.
 *
 * @since 0.1.0
 *
 * @param  string $post_content post_content
 * @return string $post_content Maybe post_content_filtered
 */
function bbp_jp_markdown_edit( $post_content = '' ) {

	// Maybe swap post_content for post_content_filtered
	if ( bbp_is_topic_edit() || bbp_is_forum_edit() || bbp_is_reply_edit() ) {
		$post_content = bbp_get_global_post_field( 'post_content_filtered', 'raw' );
	}

	return $post_content;
}
