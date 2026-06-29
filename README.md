# Netgsm Laravel

Laravel 13.x ve PHP 8.4+ projeleri için Netgsm SMS paketi.

Bu paket ile Laravel uygulamanızdan kolayca:

- normal SMS gönderebilir,
- çoklu SMS gönderebilir,
- ileri tarihli SMS planlayabilir,
- OTP SMS gönderebilir,
- SMS işlemlerini queue ve cron ile çalıştırabilirsiniz.

## Kurulum

```bash
composer require hakanispirli/netgsm-laravel
```

Config dosyasını yayınlayın:

```bash
php artisan vendor:publish --tag=netgsm-config
```

`.env` dosyanıza ekleyin:

```dotenv
NETGSM_USERNAME=850xxxxxxx
NETGSM_PASSWORD=xxxxxxxx
NETGSM_MSGHEADER=BASLIK
NETGSM_APPNAME="Laravel App"
NETGSM_DEFAULT_ENCODING=TR
NETGSM_DEFAULT_IYSFILTER=0
NETGSM_QUEUE=netgsm-sms
```

## Kullanım

```php
use HakanIspirli\NetgsmLaravel\Facades\NetgsmSms;
```

### Normal SMS

```php
$result = NetgsmSms::send(
    to: '510xxxxxxx',
    message: 'Merhaba, test mesajıdır.',
);
```

### Çoklu SMS

```php
$result = NetgsmSms::sendMany([
    ['to' => '510xxxxxxx', 'message' => 'Birinci mesaj'],
    ['to' => '511xxxxxxx', 'message' => 'İkinci mesaj'],
]);
```

### İleri Tarihli SMS

```php
$result = NetgsmSms::schedule(
    to: '510xxxxxxx',
    message: 'Planlı mesaj',
    startsAt: now()->addDay()->setTime(10, 0),
);
```

### OTP SMS

```php
$result = NetgsmSms::otp(
    to: '510xxxxxxx',
    message: 'Your verification code is 1234',
);
```

OTP gönderiminde Netgsm kuralları geçerlidir: tek alıcıya gönderilir, ileri tarihli gönderilmez, Türkçe karakter kullanılmamalıdır ve hesapta OTP paketi bulunmalıdır.

## Queue ve Cron

SMS'i kuyruğa almak için:

```php
NetgsmSms::queueSend([
    ['to' => '510xxxxxxx', 'message' => 'Kuyruk mesajı'],
]);
```

OTP'yi kuyruğa almak için:

```php
NetgsmSms::queueOtp(
    to: '510xxxxxxx',
    message: 'Your verification code is 1234',
);
```

Paylaşımlı hosting için cron script'ini yayınlayın:

```bash
php artisan vendor:publish --tag=netgsm-cron
```

Cron komutu:

```cron
* * * * * NETGSM_LARAVEL_PROJECT_DIR=/home/USER/public_html PHP_BIN=/usr/local/bin/php /bin/bash /home/USER/public_html/cron/sms.sh >/dev/null 2>&1
```

Bu script `php artisan netgsm:sms-work` komutunu çalıştırır, kuyruktaki SMS işlerini işler ve kuyruk boşalınca kapanır.

## Sonuç Formatı

Başarılı sonuç:

```php
[
    'success' => true,
    'data' => [
        'code' => '00',
        'job_id' => '17377215342605050417149344',
        'description' => 'queued',
        'warning' => null,
        'http_status' => 200,
    ],
]
```

Hatalı sonuç:

```php
[
    'success' => false,
    'message' => 'SMS gönderimi tamamlanamadı. Lütfen daha sonra tekrar deneyin.',
    'data' => [
        'code' => '70',
        'job_id' => null,
        'description' => 'Netgsm hata açıklaması',
        'warning' => null,
        'http_status' => 200,
    ],
]
```

Son kullanıcıya raw exception, stack trace, API şifresi veya OTP kodu gösterilmemelidir.

## AI ile Entegrasyon

Codex, Claude veya benzeri araçlarla paketi kendi projenize eklemek için [AI_PROMPTS.md](AI_PROMPTS.md) dosyasındaki İngilizce promptları kullanabilirsiniz. Açıklamalar Türkçe, prompt metinleri İngilizcedir.

## Lisans

MIT
