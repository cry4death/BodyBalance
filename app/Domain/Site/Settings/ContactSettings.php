<?php

namespace App\Domain\Site\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * Contacts, working hours and messengers: header, footer, contacts block, «Написать в Telegram».
 */
class ContactSettings extends Settings
{
    /** Display form, e.g. "+375 29 123-45-67". */
    public ?string $phone;

    public ?string $email;

    public string $address;

    /** Link to the studio on Yandex Maps / Google Maps. */
    public ?string $map_url;

    public ?float $latitude;

    public ?float $longitude;

    /**
     * Rows like ['days' => ['mo', 'tu'], 'opens' => '09:00', 'closes' => '21:00'].
     * The type is PHPStan-only on purpose: laravel-settings reads the plain var tag to pick a cast
     * and cannot parse array shapes.
     *
     * @phpstan-var list<array{days: list<string>, opens: string, closes: string}>
     */
    public array $opening_hours;

    /** Username without @. */
    public ?string $telegram_username;

    /** Phone number the Viber account is registered on. */
    public ?string $viber_phone;

    /** Username without @. */
    public ?string $instagram_username;

    public const array DAYS = [
        'mo' => 'Пн',
        'tu' => 'Вт',
        'we' => 'Ср',
        'th' => 'Чт',
        'fr' => 'Пт',
        'sa' => 'Сб',
        'su' => 'Вс',
    ];

    public static function group(): string
    {
        return 'contacts';
    }

    public function phoneHref(): ?string
    {
        return $this->phone ? 'tel:+'.self::digits($this->phone) : null;
    }

    public function telegramUrl(): ?string
    {
        return $this->telegram_username ? "https://t.me/{$this->telegram_username}" : null;
    }

    public function viberUrl(): ?string
    {
        return $this->viber_phone ? 'viber://chat?number=%2B'.self::digits($this->viber_phone) : null;
    }

    public function instagramUrl(): ?string
    {
        return $this->instagram_username ? "https://www.instagram.com/{$this->instagram_username}/" : null;
    }

    /**
     * Human-readable hours: ["Пн–Пт: 09:00–21:00", "Сб, Вс: 10:00–18:00"] or ["Ежедневно: 09:00–21:00"].
     *
     * @return list<string>
     */
    public function openingHoursLines(): array
    {
        return array_map(
            fn (array $row): string => self::formatDays($row['days']).': '.substr($row['opens'], 0, 5).'–'.substr($row['closes'], 0, 5),
            $this->opening_hours,
        );
    }

    /**
     * Groups consecutive days: [mo, tu, we, th, fr] → "Пн–Пт", [sa, su] → "Сб, Вс".
     *
     * @param  list<string>  $days
     */
    public static function formatDays(array $days): string
    {
        $order = array_keys(self::DAYS);
        // Positions of the selected days in week order, e.g. [sa, mo] → [0, 5].
        $indexes = array_keys(array_intersect($order, $days));

        if (count($indexes) === 7) {
            return 'Ежедневно';
        }

        $runs = [];
        foreach ($indexes as $index) {
            $last = array_key_last($runs);
            if ($last !== null && end($runs[$last]) === $index - 1) {
                $runs[$last][] = $index;
            } else {
                $runs[] = [$index];
            }
        }

        $parts = array_map(function (array $run) use ($order): string {
            $first = self::DAYS[$order[$run[0]]];
            $last = self::DAYS[$order[end($run)]];

            return match (count($run)) {
                1 => $first,
                2 => "{$first}, {$last}",
                default => "{$first}–{$last}",
            };
        }, $runs);

        return implode(', ', $parts);
    }

    /**
     * Accepts "@name", "name" or a pasted profile link and returns the bare username.
     */
    public static function normalizeUsername(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $value = trim($value);
        $value = preg_replace('~^(https?://)?(www\.)?(t\.me|telegram\.me|instagram\.com)/~i', '', $value) ?? $value;

        return trim($value, "@/ \t") ?: null;
    }

    public static function digits(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?? '';
    }

    public static function isBelarusianPhone(string $phone): bool
    {
        return (bool) preg_match('/^375\d{9}$/', self::digits($phone));
    }
}
