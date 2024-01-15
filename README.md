## Setup (Require Windows 10 22H2 or later)
1. 「.all-setup/windows.bat」を実行する (以下のセットアップが自動的に実行される)
    1. Visual Studio Code をインストールする
    2. Visual Studio Code の拡張機能「Dev Containers」をインストールする
    3. Visual Studio Code の拡張機能「Japanese Language Pack」をインストールする (Optional)
    4. Git をインストールする
    5. Git のname, email を設定する (Local scope)
    5. WSL2 をインストールする
    6. WSL2 に Docker をインストールする
2. WSL2内のストレージにこのフォルダを移動する

## How To Use (Backend)
1. Visual Studio Code を起動する
2. プロジェクトフォルダを開く
3. 画面左下の緑色のアイコン「Open a Remote Window」を選択
4. 「Reopen in Container」を選択
5. 「api」を選択 (選択後、初回はコンテナビルドのため数分間待機)
6. Visual Studio Code のタイトルバーの色が変化したことを確認する
7. Webブラウザから「http://localhost:8000」へアクセスできることを確認する

## How To Use (Frontend)
1. Visual Studio Code の「File > New Window」から新しいウインドウを開く
2. プロジェクトフォルダを開く
3. 画面左下の緑色のアイコン「Open a Remote Window」を選択
4. 「Reopen in Container」を選択
5. 「web」を選択 (選択後、初回はコンテナビルドのため数分間待機)
6. Visual Studio Code のタイトルバーの色が変化したことを確認する
7. Webブラウザから「http://localhost:3000」へアクセスできることを確認する

## Directory Structure
ディレクトリ構造
```
(root)
├--.all-setup - 初回セットアップ用シェルスクリプト (基本触らない)
├--.devcontainer - VSCodeのDevContainer設定 (基本触らない)
├--.docker - Docker設定 (基本触らない)
├--.git - Git設定 (編集禁止)
├--api - バックエンドのソースコード (Laravel)
    └-- ...
├--web - フロントエンドのソースコード (React)
    └-- ...
├--docker-compose.yml - Docker Compose 設定ファイル (基本触らない)
└--README.md 今見ているファイル
```

## Contact
@author konnohir