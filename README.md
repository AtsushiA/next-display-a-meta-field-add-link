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

[Meta Field Block（display-a-meta-field-as-block）](https://wordpress.org/plugins/display-a-meta-field-as-block/) が有効化されている必要があります。

- Meta Field Block が無効の状態でこのプラグインを有効化しようとすると、エラーメッセージを表示してキャンセルします。
- Meta Field Block が後から無効化された場合は、管理画面のプラグイン一覧にエラー通知を表示し、すべての機能を停止します。

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
| 有効化時の依存チェック | `register_activation_hook` |
| ランタイムの依存チェック・初期化 | `plugins_loaded` |
| PHP 側にカスタム属性を登録 | `block_type_metadata_settings` |
| フロントエンドでリンクをラップ | `meta_field_block_get_block_content` |
| エディタースクリプトのエンキュー | `enqueue_block_editor_assets` |
| エディターにカスタム属性を追加 | `blocks.registerBlockType`（JS） |
| サイドバーにパネルを追加 | `editor.BlockEdit`（JS） |

## 動作環境

- WordPress 5.9 以上
- PHP 7.4 以上
- Meta Field Block 1.x

## 変更履歴

### 1.1.0
- 依存プラグイン（Meta Field Block）のチェックを追加
  - 有効化時: Meta Field Block が無効なら有効化をキャンセル
  - ランタイム: Meta Field Block が無効な場合は処理をスキップ
  - 管理画面: プラグイン一覧画面に依存不足のエラー通知を表示

### 1.0.0
- 初回リリース
