FROM node:21.5.0-slim
RUN apt update \
    && apt install -y git zip unzip \
    && apt clean && rm -rf /var/lib/apt/lists/*

COPY startup.sh /startup.sh
RUN chmod 744 /startup.sh

WORKDIR /pkg/web

CMD ["/startup.sh"]
