# AI Prompt Rehberi

Bu dosyadaki açıklamalar Türkçedir. Kopyalayıp Codex, Claude veya benzeri AI kodlama aracına vereceğiniz prompt metinleri İngilizcedir.

## 1. Paketi kur ve ayarla

Laravel projenize paketi kurdurmak ve `.env` ayarlarını hazırlatmak için kullanın.

```text
You are working in my Laravel 13.x and PHP 8.4 project.

Install and configure `hakanispirli/netgsm-laravel`.

Tasks:
- Install the package with Composer.
- Publish the config with `php artisan vendor:publish --tag=netgsm-config`.
- Add or document these `.env` keys: NETGSM_USERNAME, NETGSM_PASSWORD, NETGSM_MSGHEADER, NETGSM_APPNAME, NETGSM_DEFAULT_ENCODING, NETGSM_DEFAULT_IYSFILTER, NETGSM_QUEUE.
- Do not hard-code Netgsm credentials.
- Do not expose secrets in logs, comments, tests, or terminal output.

After the changes, list the changed files and the commands I need to run.
```

## 2. Normal SMS gönderimi ekle

Bir controller veya mevcut iş akışından SMS göndermek için kullanın. AI'dan önce proje yapısını incelemesini ister.

```text
You are working in my Laravel 13.x and PHP 8.4 project.

Add SMS sending with `hakanispirli/netgsm-laravel`.

Requirements:
- First inspect the existing controller, service, job, event, or notification structure.
- Keep controllers thin.
- Put SMS logic in a service, job, listener, or notification according to the project style.
- Use `NetgsmSms::send()` or `HakanIspirli\NetgsmLaravel\Contracts\SmsSenderInterface`.
- Pass the recipient phone number and message as method arguments.
- Return a safe structured result.
- Log failures with context, but never log credentials, Authorization headers, OTP codes, or sensitive message content.
- Show users only a safe generic error message when sending fails.

After implementation, summarize the changed files.
```

## 3. Çoklu, planlı veya OTP SMS ekle

Normal gönderim dışındaki yaygın SMS senaryoları için kullanın.

```text
You are working in my Laravel 13.x and PHP 8.4 project.

Use `hakanispirli/netgsm-laravel` for one of these SMS flows:
- Multiple SMS: use `NetgsmSms::sendMany()`.
- Scheduled SMS: use `NetgsmSms::schedule()` or `NetgsmSms::scheduleMany()` with Carbon or DateTimeInterface values.
- OTP SMS: use `NetgsmSms::otp()` or `OtpSenderInterface`.

Requirements:
- Follow the existing project architecture.
- Do not manually format Netgsm dates; let the package handle scheduled SMS dates.
- Do not use bulk or scheduled SMS methods for OTP.
- Do not include Turkish characters in OTP messages.
- Do not log OTP codes or sensitive message content.
- Avoid N+1 queries if recipients are loaded from Eloquent models.
- Return the package result in the project's standard response format.

After implementation, show one example call from the workflow.
```

## 4. Queue ve cron kur

Paylaşımlı hosting veya arka planda SMS işleme ihtiyacı için kullanın.

```text
You are working in my Laravel 13.x and PHP 8.4 project.

Configure queued SMS sending with `hakanispirli/netgsm-laravel`.

Requirements:
- Use `NetgsmSms::queueSend()` for normal queued SMS.
- Use `NetgsmSms::queueOtp()` only when OTP sending should be queued.
- Keep the queue name configurable with `NETGSM_QUEUE`, defaulting to `netgsm-sms`.
- If this project is on shared hosting, publish the cron file with `php artisan vendor:publish --tag=netgsm-cron`.
- Use `php artisan netgsm:sms-work` for cron-based queue processing.
- Provide the exact cron command for cPanel or DirectAdmin.
- Do not require Supervisor or a permanently running worker.

After configuration, list the changed files and commands I need to run.
```

## 5. Hata çözümleme

SMS gönderimi başarısız olursa AI aracına bu promptu verin.

```text
You are working in my Laravel 13.x project using `hakanispirli/netgsm-laravel`.

Troubleshoot a failed Netgsm SMS send.

Requirements:
- Inspect the returned structured result and relevant application logs.
- Identify the Netgsm response code, description, warning, job id, and HTTP status when available.
- Check `.env` and `config/netgsm.php` without printing secrets.
- Verify sender header, credentials, API permissions, IP restrictions, IYS settings, queue configuration, and OTP package requirements when relevant.
- Do not expose passwords, tokens, Authorization headers, or OTP codes.
- Suggest the smallest safe fix.

Do not make unrelated refactors.
```
