#!/usr/bin/env bash

/usr/local/bin/npm run dev &
pid=$!
trap "kill -SIGINT $pid" 1 2 3 15
wait $pid
exit 0

# @ref https://qiita.com/kesoji/items/f3187e09cf999d9ca9a8
