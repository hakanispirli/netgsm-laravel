<?php

declare(strict_types=1);

namespace HakanIspirli\NetgsmLaravel\Console;

use Illuminate\Console\Command;

final class NetgsmSmsWorkCommand extends Command
{
    protected $signature = 'netgsm:sms-work
        {--queue= : Queue name override}
        {--tries= : Maximum job attempts}
        {--timeout= : Worker timeout in seconds}';

    protected $description = 'Process Netgsm SMS queue jobs once and exit when the queue is empty.';

    public function handle(): int
    {
        $queue = (string) ($this->option('queue') ?: config('netgsm.queue', 'netgsm-sms'));
        $tries = (int) ($this->option('tries') ?: config('netgsm.queue_tries', 3));
        $timeout = (int) ($this->option('timeout') ?: config('netgsm.queue_timeout', 60));
        $connection = config('netgsm.queue_connection');

        $arguments = [
            '--queue' => $queue,
            '--stop-when-empty' => true,
            '--tries' => $tries > 0 ? $tries : 3,
            '--timeout' => $timeout > 0 ? $timeout : 60,
        ];

        if (is_string($connection) && $connection !== '') {
            $arguments['connection'] = $connection;
        }

        return (int) $this->call('queue:work', $arguments);
    }
}
