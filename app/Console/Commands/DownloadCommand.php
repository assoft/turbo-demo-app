<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class DownloadCommand extends Command
{
    protected $signature = 'tailwindcss:download {--force : If the file already exists, we will not touch it. Use this flag if you want to replace it with a new version.}';
    protected $description = 'Downloads the TailwindCSS binary for the version specified in your config/tailwindcss.php.';

    public function handle()
    {
        $os = php_uname('s');
        $cpu = php_uname('m');

        $architectureToBinary = [
            'Linux-x86_64' => 'tailwindcss-linux-x64',
            'Linux-aarch64' => 'tailwindcss-linux-arm64',
        ];

        if (! $targetArchitecture = ($architectureToBinary["{$os}-{$cpu}"] ?? false)) {
            $this->error(sprintf('Looks like you are running a platform that is currently not supported (%s-%s).', $os, $cpu));

            return self::FAILURE;
        }

        $targetPath = config('tailwindcss.bin_path');
        $targetVersion = config('tailwindcss.version');

        if (File::exists($targetPath) && ! $this->option('force')) {
            $this->warn('TailwindCSS binary already exists. Use the --force flag if you want to override it.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Downloading the TailwindCSS binary (%s/%s/%s)...', $os, $cpu, $targetVersion));

        $contents = Http::get($this->downloadUrl($targetArchitecture, $targetVersion))
            ->throw()
            ->body();

        if (! $contents) {
            $this->error('Something went wrong when trying to download the TailwindCSS binary.');

            return self::FAILURE;
        }

        File::ensureDirectoryExists(dirname($targetPath));
        File::put($targetPath, $contents);
        File::chmod($targetPath, 0755);

        $this->info('Done!');

        return self::SUCCESS;
    }

    private function downloadUrl(string $architecture, string $version): string
    {
        return sprintf(
            'https://github.com/tailwindlabs/tailwindcss/releases/download/%s/%s',
            $version,
            $architecture,
        );
    }
}
