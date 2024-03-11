FROM alpine:latest
WORKDIR /etc/
RUN mkdir -p /etc/Sphinx/build

RUN apk add --no-cache python3 make git py3-pip
RUN python3 -m venv .venv && \
    source .venv/bin/activate && \
    pip install -U sphinx && \
    pip3 uninstall sphinx_rtd_theme && \
    pip3 install sphinx_rtd_theme sphinx-autobuild

CMD sphinx-autobuild -b html --host 0.0.0.0 --port 80 /etc/Sphinx/source /etc/Sphinx/build
