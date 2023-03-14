


# 開発環境構築手順 v2.0


ローカル環境に開発環境を構築する手順です。


① PHP7.3以上、MySQLが動作する環境をご用意ください。

② コマンドラインツール(Git Bashなど）を起動してください。

③ Windowsで開発している場合、以下のコマンドを実行してください。

```
exec winpty bash
```

④cd コマンドでプロジェクトをインストールする任意のディレクトリへ移動します。


⑤ GitHubからプロジェクトを取り込みます。

```
git clone git@github.com:amaraimusi/crud_base_l9.git
```

⑥開発環境のphp.iniを開きmemory_limitの容量を確認してください。「512M」だと後述のvendorインストールでメモリ不足エラーが発生しますので3Gくらいに書き換えてください。

```
memory_limit=512M ←変更前
memory_limit=3G ←変更後

```


⑦ 下記のcomposerコマンドでvendorをインストールしてください。環境に合わせたパッケージがvendorに自動インストールされます。

```
cd crud_base_l9/dev
composer update
```

※次のような書き方もできます。→「php composer.phar update」

<br>



⑧下記のComposerコマンドでLaravelのUIパッケージをインストールしてください。


```
composer require laravel/ui
```

※次のような書き方もできます。→「php composer.phar require laravel/ui」

<br>


⑦ MySQLにてcrud_base_l9データベースを作成してください。照合順序はutf8mb4_general_ciを選択してください。

```
例
CREATE DATABASE crud_base_l9 COLLATE utf8mb4_general_ci
```

⑧ crud_base_l9.sqlダンプファイル(crud_base_l9/doc/crud_base_l9.sql)をインポートしてください。

マイグレーションはご用意しておりません。phpmyadminかmysqlコマンドなどをご利用ください。


⑨.envファイルへ開発環境に合わせたDB設定を記述してください。

設定についてはLaravelの公式サイトなどを参照してください。


⑩URLへアクセスし、ログイン画面が表示されれば成功です。

```
例
http://localhost/crud_base_l9/dev/public/
```

⑪検証用のアカウントは以下の通りです。
いずれのアカウントもパスワードは「abcd1234」になります。

```
master2201     クライアント(マスター)
client_test1   クライアント
test2       病院スタッフ管理者	サンプル病院A
shoutoku    病院スタッフ管理者	サンプル病院B
```