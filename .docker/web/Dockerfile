FROM node:21.5.0-slim
RUN apt update \
    && apt install -y git zip unzip \
    && apt clean && rm -rf /var/lib/apt/lists/*

WORKDIR /pkg/web

CMD ["/usr/local/bin/npm", "start"]
