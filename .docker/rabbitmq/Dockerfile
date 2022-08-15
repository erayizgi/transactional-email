FROM rabbitmq:management

COPY rabbitmq.conf /etc/rabbitmq
COPY definitions.json /etc/rabbitmq

RUN cat /etc/rabbitmq/rabbitmq.conf
EXPOSE 5672
EXPOSE 15672