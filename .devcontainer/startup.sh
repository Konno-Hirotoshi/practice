#!/usr/bin/env bash

cd ./api
/usr/local/bin/composer start &
pid=$!
cd -

cd ./web
/usr/bin/npm install
/usr/bin/npm run dev &
pid2=$!
cd -

handler() {
    kill $pid
    kill $pid2
}
trap handler SIGTERM
wait $pid
wait $pid2
exit 0

# @ref https://qiita.com/kesoji/items/f3187e09cf999d9ca9a8
# @ref https://qiita.com/qualitia_cdev/items/2a6d96b26ac12ee66d4e
