<?php
/**
 * Plugin Name: NExT Meta Field Block add Link
 * Description: Meta Field Block（display-a-meta-field-as-block）のURLフィールドに、リンク付与と新しいタブで開くオプションを追加します。
 * Version:     1.1.0
 * Author:      NExT-Season
 * Author URI:	 https://next-season.net
 * License:     GPL-2.0-or-later
 * Text Domain: next-display-a-meta-field-add-link
 * Requires at least: 5.9
 * Requires PHP: 7.4
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

define( 'NDMFAL_VERSION', '1.1.0' );
define( 'NDMFAL_DEPENDENCY', 'display-a-meta-field-as-block/meta-field-block.php' );

// ---------------------------------------------------------------------------
// 有効化フック: 依存プラグインが無効なら有効化をキャンセルする
// ---------------------------------------------------------------------------
register_activation_hook( __FILE__, function () {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	if ( ! is_plugin_active( NDMFAL_DEPENDENCY ) ) {
		wp_die(
			'<p>' . esc_html__( 'NExT Meta Field Block add Link を有効化するには Meta Field Block（display-a-meta-field-as-block）プラグインが必要です。先に Meta Field Block を有効化してください。', 'next-display-a-meta-field-add-link' ) . '</p>',
			esc_html__( 'プラグイン有効化エラー', 'next-display-a-meta-field-add-link' ),
			[ 'back_link' => true ]
		);
	}
} );

// ---------------------------------------------------------------------------
// ランタイム初期化: plugins_loaded で依存プラグインの存在を確認してから起動する
// ---------------------------------------------------------------------------
add_action( 'plugins_loaded', function () {
	// Meta Field Block が定義する MFB 定数で依存を確認する
	if ( ! defined( 'MFB' ) ) {
		add_action( 'admin_notices', function () {
			$screen = get_current_screen();
			if ( ! $screen || $screen->base !== 'plugins' ) {
				return;
			}
			printf(
				'<div class="notice notice-error"><p>%s</p></div>',
				esc_html__( 'NExT Meta Field Block add Link を動作させるには Meta Field Block（display-a-meta-field-as-block）プラグインが必要です。', 'next-display-a-meta-field-add-link' )
			);
		} );
		return;
	}

	// PHP 側でカスタム属性を block.json の属性スキーマに追加する。
	// これにより、フロントエンドレンダリング時に $attributes へ ndmfAddLink / ndmfOpenNewTab が渡される。
	add_filter( 'block_type_metadata_settings', function ( $settings, $metadata ) {
		if ( empty( $metadata['name'] ) || $metadata['name'] !== 'mfb/meta-field-block' ) {
			return $settings;
		}

		if ( ! isset( $settings['attributes'] ) ) {
			$settings['attributes'] = [];
		}

		$settings['attributes']['ndmfAddLink'] = [
			'type'    => 'boolean',
			'default' => false,
		];

		$settings['attributes']['ndmfOpenNewTab'] = [
			'type'    => 'boolean',
			'default' => false,
		];

		return $settings;
	}, 10, 2 );

	if ( ! class_exists( 'NExT_Meta_Field_Add_Link' ) ) {

		class NExT_Meta_Field_Add_Link {

			public function __construct() {
				// フロントエンド: URLコンテンツをリンクでラップする
				add_filter( 'meta_field_block_get_block_content', [ $this, 'add_link_to_field' ], 10, 5 );

				// エディター: Inspector Controls を追加する JS をエンキューする
				add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_assets' ] );
			}

			/**
			 * ndmfAddLink が有効な場合、フィールドの値をリンクでラップする。
			 *
			 * @param string   $content     フィールドの値。
			 * @param array    $attributes  ブロック属性。
			 * @param WP_Block $block       ブロックインスタンス。
			 * @param int      $post_id     投稿 ID。
			 * @param string   $object_type オブジェクトタイプ。
			 * @return string
			 */
			public function add_link_to_field( $content, $attributes, $block, $post_id, $object_type ) {
				// リンク付与オプションが無効なら何もしない
				if ( empty( $attributes['ndmfAddLink'] ) ) {
					return $content;
				}

				// 文字列以外・空コンテンツは処理しない
				if ( ! is_string( $content ) || '' === trim( $content ) ) {
					return $content;
				}

				$trimmed = trim( $content );

				// URL として有効か検証する
				$url = esc_url( $trimmed );
				if ( empty( $url ) ) {
					return $content;
				}

				// target="_blank" の設定
				$target_attr = ! empty( $attributes['ndmfOpenNewTab'] )
					? ' target="_blank" rel="noopener noreferrer"'
					: '';

				return sprintf(
					'<a href="%1$s"%2$s>%3$s</a>',
					$url,
					$target_attr,
					esc_html( $trimmed )
				);
			}

			/**
			 * ブロックエディター用 JS をエンキューする。
			 */
			public function enqueue_editor_assets() {
				wp_enqueue_script(
					'next-display-a-meta-field-add-link-editor',
					plugin_dir_url( __FILE__ ) . 'assets/js/editor.js',
					[ 'wp-blocks', 'wp-hooks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-i18n' ],
					NDMFAL_VERSION,
					true
				);
			}
		}

		new NExT_Meta_Field_Add_Link();
	}
} );
