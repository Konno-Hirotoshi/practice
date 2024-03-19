#!/usr/bin/env bash

/usr/local/bin/npm install
/usr/local/bin/npm run dev &
pid=$!
trap "kill -SIGINT $pid" 1 2 3 15
wait $pid
exit 0
