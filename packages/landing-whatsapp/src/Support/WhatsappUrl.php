<?php

namespace Template\LandingWhatsapp\Support;

class WhatsappUrl
{
    public function make(?string $phone, ?string $message = null): string
    {
        $phone = $this->normalizePhone($phone);

        if ($phone === '') {
            return '';
        }

        $url = 'https://wa.me/'.$phone;
        $message = trim((string) $message);

        if ($message !== '') {
            $url .= '?text='.rawurlencode($message);
        }

        return $url;
    }

    public function normalizePhone(?string $phone): string
    {
        return preg_replace('/\D+/', '', (string) $phone) ?: '';
    }
}
