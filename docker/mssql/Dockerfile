ARG IMAGE_TAG
ARG USER_NAME=mssql
FROM mcr.microsoft.com/mssql/server:${IMAGE_TAG}
USER root
RUN mkdir -p /usr/src/app
WORKDIR /usr/src/app
COPY usr/src/app .
RUN chmod +x run-initialization.sh entrypoint.sh
USER ${USER_NAME}
EXPOSE 1433

CMD /bin/bash ./entrypoint.sh
