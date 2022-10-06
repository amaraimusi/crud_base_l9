


# CrudBase.min.jsおよびCrudBase.min.cssのコンパイル方法


CrudBase.min.jsは「/crud_base_l9/dev/resources/js/CrudBase」にあるjsファイル群を一つにまとめたファイルです。

CrudBase.min.jsコンパイルには**gulp**を用いています。

「/crud_base_l9/dev/resources/js/CrudBase」に存在するjsファイルを修正した場合、gulpにてコンパイルしてください。（あるいはCrudBase.min.jsを修正したい場合）

CrudBase.min.cssも同様な方法でコンパイルできます。

## gulpのインストール手順

まずgulpをnpmからインストールする必要があります。


下記のコマンドを実行してください。（下記コマンド群をまとめてコピペ実行するとよいです）

```
echo '当プロジェクトのルートディレクトリに移動します。'
cd ~/git/crud_base_l9

echo 'グローバルのgulpを一旦、アンインストールします。'
npm rm --global gulp

echo 'グローバルへgulpを再インストールします。'
npm install gulp -g

echo 'ローカルへgulpをインストールします。'
npm install --save-dev gulp

echo 'gulp-concatをインストールします。'
npm install --save-dev gulp-concat

echo 'gulp-terserをインストールします。'
npm install gulp-terser --save-dev

```

## gulpでコンパイルを実行する


CrudBase.min.jsのコンパイルはcdコマンドでルートディレクトリに移動後、下記コマンドを実行します。

```
gulp CrudBase
```

上記コマンドを実行すると「/crud_base_l9/dev/resources/js/CrudBase」に存在するjsファイル群をコンパイル（一つにまとめる）し、/crud_base_l9/dev/public/jsにCrudBase.min.jsとして出力あるいは更新します。


## CrudBase.min.cssのコンパイル

CrudBase.min.cssもjsと同様な感じでコンパイルできます。

```
gulp CrudBaseForCss
```
「/crud_base_l9/dev/resources/css/CrudBase」にあるcssファイル群をコンパイル（一つにまとめる）し、/crud_base_l9/dev/public/cssにCrudBase.min.cssとして出力します。

## コンパイルの設定を見る

コンパイルの設定は**gulpfile.js**に書かれています。
gulpfile.jsはプロジェクトのルートディレクトリに存在しています。

--------------




