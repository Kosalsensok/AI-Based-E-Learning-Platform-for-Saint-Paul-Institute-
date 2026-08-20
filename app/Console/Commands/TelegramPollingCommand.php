<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\TelegramSecurityPipeline;

class TelegramPollingCommand extends Command
{
    protected $signature = 'bot:listen';
    protected $description = 'Start SPI E-LMS Telegram Bot Long Polling Engine with 5-Layer Security';

    public function handle()
    {
        $this->info('🚀 SPI E-LMS Telegram Bot Engine is running...');
        $botToken = config('services.telegram.bot_token');

        if (empty($botToken)) {
            $this->error('❌ Telegram Bot Token is not configured.');
            return Command::FAILURE;
        }

        $offset = 0;

        while (true) {
            try {
                // ជាន់ទី ១៖ Stream Offset & Safe Long-Polling
                $response = Http::withoutVerifying()
                    ->timeout(35)
                    ->get("https://api.telegram.org/bot{$botToken}/getUpdates", [
                        'offset'          => $offset,
                        'timeout'         => 30,
                        'allowed_updates' => json_encode(['message', 'callback_query']),
                    ]);

                if ($response->successful()) {
                    $updates = $response->json('result', []);

                    foreach ($updates as $update) {
                        $offset = $update['update_id'] + 1;

                        // ដំណើរការត្រួតពិនិត្យ 5-Layer Security Pipeline
                        if (TelegramSecurityPipeline::validate($update)) {
                            $this->handleBotCommand($update);
                        }
                    }
                } else {
                    $this->error('Telegram API Response Error: ' . $response->body());
                    sleep(2);
                }
            } catch (\Exception $e) {
                $this->error('Error: ' . $e->getMessage());
                sleep(2);
            }
        }
    }

    private function handleBotCommand(array $update): void
    {
        $chatId = $update['message']['chat']['id'] ?? null;
        $text = trim($update['message']['text'] ?? '');

        if ($chatId && $text === '/start') {
            TelegramSecurityPipeline::sendMessage(
                $chatId,
                "👋 *សូមស្វាគមន៍មកកាន់ប្រព័ន្ធ SPI E-LMS!*\n\nសូមផ្ញើលេខសម្គាល់សិស្ស ឬគ្រូ ដើម្បីភ្ជាប់គណនី។",
                'Markdown'
            );
        } elseif ($chatId && str_starts_with($text, '/login')) {
            TelegramSecurityPipeline::sendMessage(
                $chatId,
                "🔐 *ចូលប្រើប្រាស់ប្រព័ន្ធ SPI E-LMS*\n\nសូមចូលទៅកាន់៖ https://spilms.tech/login",
                'Markdown'
            );
        }
    }
}
