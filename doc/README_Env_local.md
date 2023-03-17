


# ローカルへの開発環境構築手順 v2.1


ローカル環境に開発環境を構築する手順です。WindowsにGit for windowsをインストールしている人向けに説明しておりますが、Macでもおおよその流れは同じです。


■開発環境構築に必要なスキル

開発環境構築するにあたりある程度の知識が必要です。下記のスキルが求められます。


- MySQL
- PHP
- CLI（コマンドラインツール） / Linuxコマンド
- Git / GitHub
- XAMPPまたはDocker
- Laravel
- Apatch
- Composer



■PHP8.0以上、MySQLが動作する環境をご用意ください。

XAMPPが便利です。
<br>


■コマンドラインツール(Git Bashなど）を起動してください。
<br>


■Windowsで開発している場合、以下のコマンドを実行してください。

```
exec winpty bash
```

■cd コマンドでプロジェクトを配置する任意のディレクトリへ移動します。

例↓ gitディレクトリにプロジェクトを作成する場合


```
cd git
```


■GitHubからプロジェクトを取り込みます。

```
git clone git@github.com:amaraimusi/crud_base_l9.git
```

■開発環境のphp.iniを開きmemory_limitの容量を確認してください。「512M」だと後述のvendorインストールでメモリ不足エラーが発生しますので3Gくらいに書き換えてください。

```
memory_limit=512M ←変更前
memory_limit=3G ←変更後

```

■devディレクトリにcomposer.pharをダウンロードします。



```
cd crud_base_l9/dev
php -r "readfile('https://getcomposer.org/installer');" | php

```

すでにcomposer.pharが存在する場合、composerを以下のコマンドで最新にアップデートできます。


```
cd crud_base_l9/dev
composer update
```


※次のような書き方もできます。→「php composer.phar update」

<br>


■下記のcomposerコマンドでlaravel9のvendorをインストールします。


```
php composer.phar update
```




■ MySQLにてcrud_base_l9データベースを作成してください。照合順序はutf8mb4_general_ciを選択してください。

```
例
CREATE DATABASE crud_base_l9 COLLATE utf8mb4_general_ci
```

■ crud_base_l9.sqlダンプファイル(crud_base_l9/doc/crud_base_l9.sql)をインポートしてください。

マイグレーションはご用意しておりません。phpmyadminかmysqlコマンドなどをご利用ください。

<br>

■.envファイルへ開発環境に合わせたDB設定を記述してください。

devフォルダ内の.env_localファイルを.envに名前変更すると便利です。
DB名が「crud_base_l9」であり、mysqlのアカウントがroot,パスワードなしの状態ならすぐに仕える状態になっています。

<br>


参考に.envの記述例を以下にしめします。


ローカル環境での.envファイル設定例


```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=crud_base_l9
DB_USERNAME=root
DB_PASSWORD=


SESSION_DRIVER=database
```



<br>

ついでに本番環境のサーバーのおける.env設定例も示します。

サーバーにアップする場合はメール設定を上記と同じ「.env」ファイルに記載します。

レンタルサーバー（さくらインターネット）だと以下のような感じで設定します。
メール送信する機能を盛り込む予定があるのであれば、メール設定も行っておくと良いです。

```

MAIL_MAILER=smtp
MAIL_HOST=アカウント.sakura.ne.jp
MAIL_PORT=587
MAIL_USERNAME=アカウント@アカウント.sakura.ne.jp
MAIL_PASSWORD=メールのパスワード
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=アカウント@アカウント.sakura.ne.jp
MAIL_FROM_NAME="○○システム"

```


エックスサーバーの場合(動作未確認）

```
MAIL_DRIVER=smtp
MAIL_HOST=xxxxx.xserver.jp
MAIL_PORT=465
MAIL_USERNAME=xxxxx@helog.jp
MAIL_PASSWORD=xxxxxxxx
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=xxxxx@helog.jp（送信元メールアドレス）
MAIL_FROM_NAME=xxxxx（送信元名）
```

<br>







■シンボリックリンクを設定してください。（Apatchのhtdocsディレクトリにプロジェクトを作成している場合は不要です。）


Windowsでシンボリックリンクを作成するには、Windows PowerShellを管理者権限で開き、下記のシンボリックリンク作成コマンドを実行します。

パスは各自のPC環境に合わせて書き換えてください。

```
cmd /c mklink /D C:\xampp\htdocs\crud_base_l9 C:\Users\user\git\crud_base_l9
```

※Windows PowerShellを管理者権限で開く方法→Windows PowerShellのアイコンを右クリック→「管理者として実行する」

<br>





■URLへアクセスし、ログイン画面が表示されれば成功です。

```
例
http://localhost/crud_base_l9/dev/public/
```

■検証用のアカウントは以下の通りです。
いずれのアカウントもパスワードは「abcd1234」になります。

```
himiko                        管理者権限
yamatotakeru@example.com      管理者権限
ni_n_toku_tennou@example.com  管理者権限
```

他にも数件のユーザーアカウントをご用意しています。詳しくはデータベースのusersテーブルを参照してください。

<br><br>