#!/usr/bin/env bash
set -e

# ==============================================================================
# SCRIPT PEMBARUAN & PERBAIKAN MASSAL 3 DOMAIN OJS MADANI
# Target: jpstem.indonesiamadani.com, jpmm.indonesiamadani.com, jham.indonesiamadani.com
# ==============================================================================

DOMAINS=(
    "jpstem.indonesiamadani.com"
    "jpmm.indonesiamadani.com"
    "jham.indonesiamadani.com"
)

echo "===================================================================="
echo "   MEMULAI PROSES PERBAIKAN & PEMBARUAN PADA 3 DOMAIN PRODUCTION    "
echo "===================================================================="

for domain in "${DOMAINS[@]}"; do
    TARGET_DIR="/home/u218101292/domains/${domain}/public_html"
    echo ""
    echo "--------------------------------------------------------------------"
    echo ">> Memproses Domain: https://${domain}"
    echo ">> Direktori: ${TARGET_DIR}"
    echo "--------------------------------------------------------------------"

    if [ -d "$TARGET_DIR" ]; then
        cd "$TARGET_DIR"

        # 1. Tarik commit terbaru dari GitHub
        echo "1. Menarik pembaruan dari repository GitHub (origin/main)..."
        git fetch origin main
        git reset --hard origin/main

        # 2. Bersihkan file cache konfigurasi yang berpotensi corrupt
        echo "2. Membersihkan bootstrap cache..."
        rm -f bootstrap/cache/*.php

        # 3. Jalankan migrasi database (termasuk enum to string untuk users.role)
        echo "3. Menjalankan migrasi database skema baru..."
        php artisan migrate --force

        # 4. Bersihkan dan segarkan cache framework
        echo "4. Mengoptimasi dan membersihkan cache aplikasi..."
        php artisan optimize:clear

        echo ">>> [SUKSES] Domain ${domain} berhasil diperbarui 100%!"
    else
        echo ">>> [GAGAL] Direktori ${TARGET_DIR} tidak ditemukan!"
    fi
done

echo ""
echo "===================================================================="
echo "  SEMUA PROSES SELESAI! KETIGA DOMAIN TELAH SIAP DIGUNAKAN          "
echo "===================================================================="
