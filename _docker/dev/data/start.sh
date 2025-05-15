#!/bin/bash

if [[ "${OCTANE_HOST}" == "" ]]
then
    OCTANE_HOST="0.0.0.0"
fi

if [[ "${OCTANE_PORT}" == "" ]]
then
    OCTANE_PORT=8881
fi

if [[ "${OCTANE_WATCH}" == "" ]]
then
    OCTANE_WATCH="false"
fi

echo "FZKC_PROJECT_NAME = ${FZKC_PROJECT_NAME}"
echo "FZKC_CASTLE_NAME = ${FZKC_CASTLE_NAME}"
echo "FZKC_CASTLE_PORT = ${FZKC_CASTLE_PORT}"
echo ""
echo "FZKC_DNS_HOSTNAME = ${FZKC_CASTLE_NAME}.${FZKC_PROJECT_NAME}.space"
echo "FZKC_NETWORK ${FZKC_NETWORK}"
echo "FZKC_NETWORK_GATEWAY_IP = ${FZKC_NETWORK_GATEWAY_IP}"
echo "FZKC_NETWORK_DNS_IP = ${FZKC_NETWORK_DNS_IP}"
echo ""
echo "OCTANE_HOST = ${OCTANE_HOST}"
echo "OCTANE_PORT = ${OCTANE_PORT}"
echo "OCTANE_WATCH = ${OCTANE_WATCH}"

if [[ "${OCTANE_WATCH}" == "true" ]]
then
    COMMAND="/usr/local/bin/php /app/artisan octane:start --host=${OCTANE_HOST} --port=${OCTANE_PORT} --watch"
else
    COMMAND="/usr/local/bin/php /app/artisan octane:start --host=${OCTANE_HOST} --port=${OCTANE_PORT}"
fi

chmod -R 666 /app/storage/logs/*

cat /etc/supervisor/supervisord.skel.conf | sed s@__COMMAND_PLACEHOLDER__@"${COMMAND}"@ > /etc/supervisor/supervisord.conf

/init_dns.sh

supervisord -n -c /etc/supervisor/supervisord.conf