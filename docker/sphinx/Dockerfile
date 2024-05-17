FROM alpine:latest
WORKDIR /docs
RUN mkdir -p /etc/Sphinx/build
ADD requirements.txt /etc/Sphinx/requirements.txt

RUN apk add --no-cache python3 make git py3-pip
RUN python3 -m venv .venv && \
    source .venv/bin/activate && \
    pip install -r /etc/Sphinx/requirements.txt

CMD .venv/bin/sphinx-autobuild -b html --host 0.0.0.0 --port 80 /etc/Sphinx/source /etc/Sphinx/build
