#!/bin/bash
# Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.

set -e

ATTACHMENTS_DIR="/var/www/html/storage/attachments"

mkdir -p "${ATTACHMENTS_DIR}"
chown -R www-data:www-data "${ATTACHMENTS_DIR}"
chmod 0750 "${ATTACHMENTS_DIR}"

exec docker-php-entrypoint "$@"
