cd %~dp0

# Update Old Component
# (最新のWindowsなら不要かもしれないが、実験時にトラブルがあったので念の為)
winget install "App Installer" --source msstore
winget install "Windows Subsystem for Linux" --source msstore

# VSCode: Visual Studio Code
winget install --id Microsoft.VisualStudioCode -e
code --install-extension ms-vscode-remote.remote-containers
code --install-extension ms-ceintl.vscode-language-pack-ja

# Git
winget install --id Git.Git -e --source winget

# Ubuntu on WSL2: Windows Subsystem for Linux 2
wsl --install -d Ubuntu
