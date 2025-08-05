# Git Auto Deployment URLs

## GitHub Webhook URL

Agar GitHub ishlatayotgan bo'lsangiz:

```
https://yourdomain.com/webhook
```

**GitHub Webhook sozlamalari:**
- **Payload URL:** `https://yourdomain.com/webhook`
- **Content type:** `application/json`
- **Secret:** Sizning webhook secret keyingiz
- **Events:** Push events
- **Branch filter:** `main` yoki `master`

## GitLab Webhook URL

Agar GitLab ishlatayotgan bo'lsangiz:

```
https://yourdomain.com/webhook
```

**GitLab Webhook sozlamalari:**
- **URL:** `https://yourdomain.com/webhook`
- **Secret Token:** Sizning webhook secret keyingiz
- **Trigger:** Push events
- **Branch filter:** `main` yoki `master`

## Bitbucket Webhook URL

Agar Bitbucket ishlatayotgan bo'lsangiz:

```
https://yourdomain.com/webhook
```

**Bitbucket Webhook sozlamalari:**
- **URL:** `https://yourdomain.com/webhook`
- **Secret:** Sizning webhook secret keyingiz
- **Triggers:** Repository push
- **Branch filter:** `main` yoki `master`

## Webhook Secret Key

Webhook secret key yaratish uchun:

```bash
openssl rand -hex 32
```

Bu komandadan kelgan natijani `.env` faylidagi `WEBHOOK_SECRET` o'zgaruvchisiga qo'yng:

```env
WEBHOOK_SECRET=your_generated_secret_here
```

## Test URL

Webhook ishlayotganini tekshirish uchun:

```bash
curl -X POST https://yourdomain.com/webhook \
  -H "Content-Type: application/json" \
  -H "X-Hub-Signature-256: sha256=test" \
  -d '{"ref":"refs/heads/main"}'
```

## Xavfsizlik

1. **Secret key** kuchli bo'lishi kerak
2. **HTTPS** ishlatish majburiy
3. **IP cheklash** qo'shish tavsiya etiladi
4. **Log fayllarini** muntazam tekshiring

## Muammolarni hal qilish

### 1. 403 Forbidden xatosi
- Webhook secret key to'g'ri ekanligini tekshiring
- `.env` faylidagi `WEBHOOK_SECRET` to'g'ri ekanligini tekshiring

### 2. 500 Internal Server Error
- `deploy.sh` fayli mavjudligini tekshiring
- Permissions to'g'ri ekanligini tekshiring
- Log fayllarini tekshiring

### 3. Webhook ishlamayapti
- URL to'g'ri ekanligini tekshiring
- Server xizmatlari ishlayotganini tekshiring
- Nginx konfiguratsiyasini tekshiring

## Monitoring

```bash
# Real-time log monitoring
tail -f /var/log/deploy.log
tail -f storage/logs/webhook.log

# Webhook status
curl -I https://yourdomain.com/webhook
```

## Foydali komandalar

```bash
# Webhook secret key yaratish
openssl rand -hex 32

# Test webhook
curl -X POST https://yourdomain.com/webhook \
  -H "Content-Type: application/json" \
  -H "X-Hub-Signature-256: sha256=test" \
  -d '{"ref":"refs/heads/main"}'

# Log fayllarini ko'rish
tail -f /var/log/deploy.log
tail -f storage/logs/webhook.log
``` 