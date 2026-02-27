/**
 * NExT Meta Field Block add Link - エディター用スクリプト
 *
 * Meta Field Block (mfb/meta-field-block) に対して
 * - リンクの付与 (ndmfAddLink)
 * - 新しいタブで開く (ndmfOpenNewTab)
 * の Inspector Controls を追加する。
 */
( function ( wp ) {
	var addFilter      = wp.hooks.addFilter;
	var __             = wp.i18n.__;
	var el             = wp.element.createElement;
	var Fragment       = wp.element.Fragment;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody      = wp.components.PanelBody;
	var ToggleControl  = wp.components.ToggleControl;

	var TARGET_BLOCK = 'mfb/meta-field-block';
	var NAMESPACE    = 'next-display-a-meta-field-add-link';

	// ---------------------------------------------------------------
	// Step 1: ブロックタイプにカスタム属性を追加する
	// ---------------------------------------------------------------
	function addLinkAttributes( settings, name ) {
		if ( name !== TARGET_BLOCK ) {
			return settings;
		}

		return Object.assign( {}, settings, {
			attributes: Object.assign( {}, settings.attributes, {
				ndmfAddLink: {
					type: 'boolean',
					default: false,
				},
				ndmfOpenNewTab: {
					type: 'boolean',
					default: false,
				},
			} ),
		} );
	}

	addFilter(
		'blocks.registerBlockType',
		NAMESPACE + '/add-attributes',
		addLinkAttributes
	);

	// ---------------------------------------------------------------
	// Step 2: Inspector Controls にリンク設定パネルを追加する
	// ---------------------------------------------------------------
	function withLinkControls( BlockEdit ) {
		return function ( props ) {
			// 対象ブロック以外はそのまま返す
			if ( props.name !== TARGET_BLOCK ) {
				return el( BlockEdit, props );
			}

			var attributes    = props.attributes;
			var setAttributes = props.setAttributes;

			return el(
				Fragment,
				null,
				// 元の BlockEdit はそのまま描画する
				el( BlockEdit, props ),
				// Inspector Controls にパネルを追加する
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{
							title: __( 'リンク設定', 'next-display-a-meta-field-add-link' ),
							initialOpen: true,
						},
						// リンクを付与するトグル
						el( ToggleControl, {
							label: __( 'リンクを付与する', 'next-display-a-meta-field-add-link' ),
							checked: !! attributes.ndmfAddLink,
							onChange: function ( value ) {
								setAttributes( { ndmfAddLink: value } );
							},
						} ),
						// 新しいタブで開くトグル（リンク付与が有効な場合のみ表示）
						attributes.ndmfAddLink
							? el( ToggleControl, {
								label: __( '新しいタブで開く（target="_blank"）', 'next-display-a-meta-field-add-link' ),
								checked: !! attributes.ndmfOpenNewTab,
								onChange: function ( value ) {
									setAttributes( { ndmfOpenNewTab: value } );
								},
							} )
							: null
					)
				)
			);
		};
	}

	addFilter(
		'editor.BlockEdit',
		NAMESPACE + '/with-link-controls',
		withLinkControls
	);
} )( window.wp );
