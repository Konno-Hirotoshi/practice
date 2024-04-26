## 🔰 Setup

* 本プロジェクトの開発環境は Dev Container (VS Code)により構築する。
* インフラは Docker on Ubuntu on WSL2 により構築する。
* 初回セットアップ手順を以下に示す。

1. ローカルマシンのターミナルで以下のコマンドを実行する
    ```
    winget install "App Installer" --source msstore
    winget install "Windows Subsystem for Linux" --source msstore
    winget install --id Microsoft.VisualStudioCode -e
    code --install-extension ms-vscode-remote.remote-containers
    code --install-extension ms-ceintl.vscode-language-pack-ja
    wsl --install -d Ubuntu
    ```

2. 自動的にUbuntu on WSL2のターミナルが開くので以下の操作を行う
    1. ユーザー名を入力する (メールアドレスの@より左側を入力する)
    2. パスワードを入力する (任意のパスワード)
    3. パスワードを再入力する

3. Ubuntu on WSL2のターミナルで以下のコマンドを実行する
    ```
    cd ~
    git clone https://github.com/Konno-Hirotoshi/practice.git &&
    cd practice &&
    git config user.name "$(whoami)" &&
    git config user.email "$(whoami)@localhost" &&
    code .
    ```

4. 起動したVisual Studio Code 上で以下の操作を行う
    1. 画面左下の緑色のアイコン「Open a Remote Window」を選択
    2. 「Reopen in Container」を選択 (選択後、初回はコンテナビルドのため数分間待機)
    3. Visual Studio Code のタイトルバーの色が変化したことを確認する

5. Webブラウザから http://localhost:5173 へアクセスできることを確認する

## 🔧 Development
初回セットアップ後の開発手順を以下に示す。

1. Visual Studio Code を起動する
2. 履歴からpractice@desktop-linuxプロジェクトを選択する

## 📁 Directory Structure
ディレクトリ構造を以下に示す。
```
(root)
├--.devcontainer - Dev Container, Docker設定 (基本触らない)
├--.git - Git設定 (編集禁止)
├--api - バックエンドのソースコード (Laravel)
|   └-- ...
├--web - フロントエンドのソースコード (React)
|   └-- ...
└--README.md 今見ているファイル
```

## ✉️ Contact
@author konnohir
