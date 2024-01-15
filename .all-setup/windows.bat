cd %~dp0 && cd ..

:INPUT_LOOP
SET NAMESTR=
SET /P NAMESTR="メールアドレスの@より左の部分を入力してください"
IF "%NAMESTR%"=="" GOTO :INPUT_LOOP

# VSCode: Visual Studio Code
winget install --id Microsoft.VisualStudioCode -e
code --install-extension ms-vscode-remote.remote-containers
code --install-extension ms-ceintl.vscode-language-pack-ja

# Git
winget install --id Git.Git -e --source winget
git config user.name "%NAMESTR%"
git config user.email "%NAMESTR%@localhost"

# WSL2: Windows Subsystem for Linux 2
wsl --install -d Ubuntu

# Docker in WSL2
