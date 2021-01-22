# 『CodeIgniter徹底入門』のサンプルアプリケーションをCodeIgniter4にアップデート

ここは『CodeIgniter徹底入門』（翔泳社）に含まれている以下のサンプルアプリケーション（CodeIgniter 1.6.1用）を [CodeIgniter 3.xで動作するように更新したもの](https://github.com/kenjis/codeigniter-tettei-apps) を、CodeIgniter4で動作するように更新するためのプロジェクトです（作業中）。

- コンタクトフォーム（7章）
- モバイル対応簡易掲示板（8章）
- 簡易ショッピングサイト（9章）

## 動作確認環境

- CodeIgniter 4.0.5-dev ([ci4-app-template](https://github.com/kenjis/ci4-app-template)を使用)
- PHP 7.4.13
    - Composer 2.0.8
- MySQL 5.7

## 「CodeIgniter 3.xで動作するように更新したもの」からの変更点

- @TODO

追加されたComposerのパッケージ

* CodeIgniter 3 to 4 Migration Helper <https://github.com/kenjis/ci3-to-4-migration-helper>

## インストール方法

### ダウンロード

https://github.com/kenjis/codeigniter4-tettei-apps/archive/develop.zip をダウンロードし解凍します。

### Apacheの設定

`codeigniter4-tettei-apps/public`フォルダが公開フォルダです。ここを <http://localhost:8080/> でアクセスできるように設定してください。

なお、`.htaccess`によるmod_rewriteの設定を有効にしてください。

### ファイルのパーミッション設定

必要な場合は、以下のフォルダにApacheから書き込みできる権限を付与してください。

```
$ cd /path/to/codeigniter4-tettei-apps/
$ chmod -R o+w writable/
$ chmod o+w public/captcha/
```

### 依存パッケージのインストール

Composerで依存パッケージをインストールします。

```
$ composer install
```

### データベースとユーザの作成

MySQLにデータベースとユーザを作成します。

```
CREATE DATABASE `codeigniter` DEFAULT CHARACTER SET utf8mb4;
GRANT ALL PRIVILEGES ON codeigniter.* TO username@localhost IDENTIFIED BY 'password';
```

### データベースマイグレーションとシーディングの実行

データベースにテーブルを作成し、テストデータを挿入します。

@TODO

## テストの実行方法

### PHPUnitによるアプリケーションテスト

@TODO

## ライセンス

サンプルアプリケーションのライセンスは「修正BSDライセンス」です。詳細は、[LICENSE.md](LICENSE.md) をご覧ください。

## 謝辞

サンプルアプリケーションのデザインは、神野みちるさん（株式会社ステップワイズ）にしていただきました。

## 『CodeIgniter徹底入門』について

* [『CodeIgniter徹底入門』のサンプルアプリケーションをCodeIgniter 3.xにアップデート](https://github.com/kenjis/codeigniter-tettei-apps)
* [『CodeIgniter徹底入門』情報ページ](http://codeigniter.jp/tettei/)
* [『CodeIgniter徹底入門』に対するノート](https://github.com/codeigniter-jp/codeigniter-tettei-note)
