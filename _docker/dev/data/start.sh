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

CURRENT_IP="$(cat /etc/hosts | grep $FZKC_CASTLE_NAME.$FZKC_PROJECT_NAME.space | cut -f 1 -)"

echo ""
echo "FZKC_PROJECT_NAME = ${FZKC_PROJECT_NAME}"
echo "FZKC_CASTLE_NAME = ${FZKC_CASTLE_NAME}"
echo "FZKC_CASTLE_PORT = ${FZKC_CASTLE_PORT}"
echo ""
echo "FZKC_DNS_HOSTNAME = ${FZKC_CASTLE_NAME}.${FZKC_PROJECT_NAME}.space"
echo "FZKC_NETWORK ${FZKC_NETWORK}"
echo "FZKC_NETWORK_GATEWAY_IP = ${FZKC_NETWORK_GATEWAY_IP}"
echo "FZKC_NETWORK_DNS_IP = ${FZKC_NETWORK_DNS_IP}"
echo ""
echo "CURRENT_CASTLE_IP = ${CURRENT_IP}"
echo ""
echo "OCTANE_HOST = ${OCTANE_HOST}"
echo "OCTANE_PORT = ${OCTANE_PORT}"
echo "OCTANE_WATCH = ${OCTANE_WATCH}"
echo ""

if [[ "${OCTANE_WATCH}" == "true" ]]
then
    export COMMAND="/usr/local/bin/php /app/artisan octane:start --host=${OCTANE_HOST} --port=${OCTANE_PORT} --watch"
else
    export COMMAND="/usr/local/bin/php /app/artisan octane:start --host=${OCTANE_HOST} --port=${OCTANE_PORT}"
fi

chmod -R 666 /app/storage/logs/*.log

/bin/envsubst < /etc/supervisor/supervisord.skel.conf > /etc/supervisor/supervisord.conf

/init_dns.sh

/usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
