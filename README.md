## 🔰 Setup
初回セットアップ手順を以下に示す。

1. 「.all-setup/windows.bat」を実行する (以下のセットアップが自動的に実行される)
    1. wingetコマンドを最新版に更新する
    2. WSLコンポーネントを最新版に更新する
    3. Visual Studio Code をインストールする
    4. Visual Studio Code の拡張機能「Dev Containers」をインストールする
    5. Visual Studio Code の拡張機能「Japanese Language Pack」をインストールする (Optional)
    6. Git をインストールする
    7. Ubuntu on WSL2 をインストールする

2. 自動的にUbuntu on WSL2のターミナルが開くので以下の操作を行う
    1. ユーザー名を入力する (メールアドレスの@より左側を入力する)
    2. パスワードを入力する (任意のパスワード)
    3. パスワードを再入力する
    4. 以下のコマンドを入力してVisual Studio Code を起動する
        ```
        cd ~
        git clone https://github.com/Konno-Hirotoshi/practice.git &&
        cd practice &&
        git config user.name "$(whoami)" &&
        git config user.email "$(whoami)@localhost" &&
        code .
        ```

3. 起動したVisual Studio Code 上で以下の操作を行う
    1. 画面左下の緑色のアイコン「Open a Remote Window」を選択
    2. 「Reopen in Container」を選択
    3. 「api」を選択 (選択後、初回はコンテナビルドのため数分間待機)
    4. Visual Studio Code のタイトルバーの色が変化したことを確認する
    5. 画面左下の緑色のアイコンを選択して、「Reopen Folder Locally」を選択
    6. 再度画面左下の緑色のアイコン「Open a Remote Window」を選択
    7. 「Reopen in Container」を選択
    8. 「web」を選択 (選択後、初回はコンテナビルドのため数分間待機)
    9. Visual Studio Code のタイトルバーの色が変化したことを確認する
    10. 画面左下の緑色のアイコンを選択して、「Reopen Folder Locally」を選択
    11. Visual Studio Code を終了する

4. Webブラウザから http://localhost:3000 へアクセスできることを確認する

## 🔧 Development
初回セットアップ後の開発手順を以下に示す。

1. Visual Studio Code を起動する
2. 履歴からpractice@desktop-linuxプロジェクトを選択する (api, web は区別できないので勘で選ぶ. )

#### NOTE:

* api, web どちらかのプロジェクトを開いた時点で両方のコンテナが起動する (内部的にはdocker compose up -d される)
* 両方開きたい場合は「File > New Window」から新しいウインドウを開く
* ローカルフォルダ (タイトルバーに色がつかない)では各種設定が適用されないため、開発は行わないこと

## 📁 Directory Structure
ディレクトリ構造を以下に示す。
```
(root)
├--.all-setup - 初回セットアップ用シェルスクリプト (基本触らない)
├--.devcontainer - VSCodeのDevContainer設定 (基本触らない)
├--.docker - Docker設定 (基本触らない)
├--.git - Git設定 (編集禁止)
├--api - バックエンドのソースコード (Laravel)
|   └-- ...
├--web - フロントエンドのソースコード (React)
|   └-- ...
├--docker-compose.yml - Docker Compose 設定ファイル (基本触らない)
└--README.md 今見ているファイル
```

## ✉️ Contact
@author konnohir
