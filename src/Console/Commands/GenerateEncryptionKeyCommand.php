<?php

namespace Laraditz\TngEwallet\Console\Commands;

use Illuminate\Console\Command;

class GenerateEncryptionKeyCommand extends Command
{
    protected $signature = 'tng-ewallet:generate-key';

    protected $description = 'Generate a key for TNG_ENCRYPTION_KEY';

    public function handle(): int
    {
        $key = 'base64:'.base64_encode(random_bytes(32));

        $this->line($key);
        $this->info('Add this to your .env file as TNG_ENCRYPTION_KEY. This key is never meant to rotate — see the README before changing it.');

        return self::SUCCESS;
    }
}
