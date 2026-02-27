# NExT Meta Field Block add Link

[Meta Field Block（display-a-meta-field-as-block）](https://wordpress.org/plugins/display-a-meta-field-as-block/) のブロックに、リンク付与と新しいタブで開くオプションを追加するプラグインです。

## 機能

Meta Field Block でURLフィールドを出力する際、ブロックのサイドバー（Inspector Controls）から以下の設定を行えます。

- **リンクを付与する** — フィールドの値を `<a href="...">` でラップしてリンクにする
- **新しいタブで開く** — `target="_blank" rel="noopener noreferrer"` を付与する（リンク付与が ON のときのみ表示）

## 出力例

### 設定前

```html
<div class="is-acf-field is-url-field wp-block-mfb-meta-field-block">
  <div class="value">http://example.com/</div>
</div>
```

### リンクのみ ON

```html
<div class="is-acf-field is-url-field wp-block-mfb-meta-field-block">
  <div class="value"><a href="http://example.com/">http://example.com/</a></div>
</div>
```

### リンク + 新しいタブ ON

```html
<div class="is-acf-field is-url-field wp-block-mfb-meta-field-block">
  <div class="value"><a href="http://example.com/" target="_blank" rel="noopener noreferrer">http://example.com/</a></div>
</div>
```

## 依存プラグイン

- [Meta Field Block（display-a-meta-field-as-block）](https://wordpress.org/plugins/display-a-meta-field-as-block/) が有効化されている必要があります。

## ファイル構成

```
next-display-a-meta-field-add-link/
├── next-display-a-meta-field-add-link.php   メインファイル
└── assets/js/
    └── editor.js                             ブロックエディター用スクリプト
```

## 仕組み

| 処理 | 使用フック |
|------|-----------|
| PHP 側にカスタム属性を登録 | `block_type_metadata_settings` |
| フロントエンドでリンクをラップ | `meta_field_block_get_block_content` |
| エディタースクリプトのエンキュー | `enqueue_block_editor_assets` |
| エディターにカスタム属性を追加 | `blocks.registerBlockType`（JS） |
| サイドバーにパネルを追加 | `editor.BlockEdit`（JS） |

## 動作環境

- WordPress 5.9 以上
- PHP 7.4 以上
- Meta Field Block 1.x
