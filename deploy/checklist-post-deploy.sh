#!/bin/bash
# Checklist post-deploy GMS — ejecutar en el servidor real (Termux).
# Uso: BASE=https://gym.saul-dev.com bash deploy/checklist-post-deploy.sh
# Cada bloque imprime ESPERADO vs OBTENIDO. Todo debe decir OK al final.
set -u
BASE="${BASE:-https://gym.saul-dev.com}"
FALLOS=0

ok()  { echo "OK   | $1"; }
mal() { echo "FALLA| $1 (esperado: $2 | obtenido: $3)"; FALLOS=$((FALLOS+1)); }

echo "=== 1. Cabeceras (curl -I $BASE) ==="
HEADERS=$(curl -sSI "$BASE" | tr -d '\r')
for h in "X-Content-Type-Options: nosniff" "X-Frame-Options: DENY" "Referrer-Policy: strict-origin-when-cross-origin" "Permissions-Policy: camera=(), microphone=(), geolocation=()" "Strict-Transport-Security: max-age=31536000; includeSubDomains"; do
  nombre="${h%%:*}"
  if echo "$HEADERS" | grep -qi "^$nombre:"; then
    valor=$(echo "$HEADERS" | grep -i "^$nombre:" | cut -d' ' -f2-)
    [ "$valor" = "${h#*: }" ] && ok "$nombre" || mal "$nombre" "${h#*: }" "$valor"
  else
    mal "$nombre" "presente" "ausente"
  fi
done
echo "$HEADERS" | grep -qi "^Content-Security-Policy:.*default-src 'self'" \
  && ok "CSP default-src 'self'" \
  || mal "CSP" "default-src 'self' presente" "ausente o distinta"
for src in "cdn.jsdelivr.net" "cdnjs.cloudflare.com" "code.jquery.com" "challenges.cloudflare.com"; do
  echo "$HEADERS" | grep -qi "$src" && ok "CSP permite $src" || mal "CSP $src" "permitido" "ausente"
done

echo "=== 2. Rutas denegadas (todas 404) ==="
for ruta in "/.git/config" "/.env" "/config/config.php" "/database/" "/vendor/" "/tests/" "/uploads/" "/storage/" "/uploads/comprobantes/x.pdf"; do
  code=$(curl -so /dev/null -w "%{http_code}" "$BASE$ruta")
  [ "$code" = "404" ] && ok "$ruta -> 404" || mal "$ruta" "404" "$code"
done

echo "=== 3. Límite 6MB de Nginx vs 5MB de PHP ==="
dd if=/dev/zero of=/tmp/qa_6m.bin bs=1M count=6 2>/dev/null
code=$(curl -so /dev/null -w "%{http_code}" -X POST "$BASE/api/upload_comprobante.php" -F "archivo=@/tmp/qa_6m.bin" -F "persona_id=1" -F "folio_cliente=CLI-XXXXXX")
# Nginx debe cortar con 413 antes de que PHP responda su JSON de 5MB
[ "$code" = "413" ] && ok "6MB -> 413 de Nginx" || mal "6MB" "413" "$code"
rm -f /tmp/qa_6m.bin

echo "=== 4. SRI (verificar en navegador, ver pasos manuales abajo) ==="
echo "INFO | Los hashes están en includes/head.php e includes/footer.php"

echo ""
echo "=== PASOS MANUALES DE NAVEGADOR ==="
echo "[ ] Abrir cada página (index, pagina, personas, membresias, productos,"
echo "    visitantes, recepcionistas, reportes, transferencias_cliente) con"
echo "    DevTools > Consola: CERO errores 'Content Security Policy'."
echo "    ESPERADO: consola limpia en las 9 páginas."
echo "[ ] DevTools > Red: jquery, bootstrap, sweetalert, toastify, chart.js"
echo "    cargan con código 200 (un hash SRI incorrecto los bloquea en"
echo "    silencio: aparecerían como 'blocked' o fallidos en Red)."
echo "[ ] Subir comprobante de 6MB desde el portal: ESPERADO error genérico"
echo "    del servidor (413), NO el mensaje 'supera los 5MB' de la app."
echo "[ ] Subir comprobante válido pequeño: ESPERADO éxito normal."
echo ""
if [ "$FALLOS" -eq 0 ]; then echo "TODO OK"; else echo "FALLOS: $FALLOS"; fi
exit $FALLOS
