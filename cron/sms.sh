#!/usr/bin/env bash

set -euo pipefail

PROJECT_DIR="${NETGSM_LARAVEL_PROJECT_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)}"
PHP_BIN="${PHP_BIN:-php}"

cd "$PROJECT_DIR"
"$PHP_BIN" artisan netgsm:sms-work
