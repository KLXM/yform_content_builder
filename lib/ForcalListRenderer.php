<?php

namespace KLXM\YFormContentBuilder;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use rex;
use rex_addon;
use rex_clang;
use rex_logger;
use rex_media_manager;
use rex_sql;
use Throwable;

/**
 * ForcalListRenderer
 *
 * Liefert kommende Termine aus dem forcal-Addon fuer das forcal_list Element.
 *
 * Zwei Modi:
 *  - "categories": Naechste N Termine aus einer (oder allen) Kategorien.
 *  - "repeat":     Naechste N Wiederholungen eines bestimmten Termins (Picker).
 *
 * URL-Pattern: optional, mit Platzhalter {id} (z.B. "/termine/?id={id}").
 *
 * @author  Friends Of REDAXO
 */
final class ForcalListRenderer
{
    /** @var list<string> */
    public const ALLOWED_LAYOUTS = ['cards', 'list', 'compact'];

    public const MAX_LIMIT = 50;

    /**
     * @param array<string,mixed> $elementData
     * @return array{layout:string, items:list<array<string,mixed>>, error:?string, limit:int}
     */
    public static function fetch(array $elementData): array
    {
        $layout = (string) ($elementData['layout'] ?? 'cards');
        if (!in_array($layout, self::ALLOWED_LAYOUTS, true)) {
            $layout = 'cards';
        }

        $limit = (int) ($elementData['limit'] ?? 5);
        if ($limit < 1) {
            $limit = 5;
        }
        if ($limit > self::MAX_LIMIT) {
            $limit = self::MAX_LIMIT;
        }

        $teaserLength = (int) ($elementData['teaser_length'] ?? 160);
        if ($teaserLength < 30) {
            $teaserLength = 30;
        }
        if ($teaserLength > 800) {
            $teaserLength = 800;
        }

        $mode = (string) ($elementData['mode'] ?? 'categories');
        if (!in_array($mode, ['categories', 'repeat'], true)) {
            $mode = 'categories';
        }

        $urlPattern = (string) ($elementData['url_pattern'] ?? '');
        $imageField = trim((string) ($elementData['image_field'] ?? ''));
        $showImage = !empty($elementData['show_image']);

        if (!self::isAvailable()) {
            return self::err($layout, 'Das Forcal-Addon ist nicht verfuegbar.');
        }

        try {
            if ('repeat' === $mode) {
                $items = self::fetchRepeating($elementData, $limit, $teaserLength, $urlPattern, $imageField, $showImage);
            } else {
                $items = self::fetchByCategories($elementData, $limit, $teaserLength, $urlPattern, $imageField, $showImage);
            }
        } catch (Throwable $e) {
            rex_logger::logException($e);
            return self::err($layout, 'Fehler beim Laden der Termine.');
        }

        return [
            'layout' => $layout,
            'items' => $items,
            'error' => null,
            'limit' => $limit,
        ];
    }

    /**
     * @param array<string,mixed> $elementData
     * @return list<array<string,mixed>>
     */
    private static function fetchByCategories(array $elementData, int $limit, int $teaserLength, string $urlPattern, string $imageField = '', bool $showImage = false): array
    {
        $cats = self::parseCategoryIds($elementData['categories'] ?? '');

        $factory = \forCal\Factory\forCalEventsFactory::create()
            ->from('now')
            ->to('+24 months')
            ->sortBy('start_date', 'asc');

        if ([] !== $cats) {
            $factory->inCategories($cats);
        }

        /** @var list<array<string,mixed>> $entries */
        $entries = $factory->get();
        $items = [];

        foreach ($entries as $event) {
            $item = self::makeItem($event, $teaserLength, $urlPattern, $imageField, $showImage);
            if (null === $item) {
                continue;
            }
            $items[] = $item;
            if (count($items) >= $limit) {
                break;
            }
        }

        usort($items, static fn (array $a, array $b): int => $a['sort_key'] <=> $b['sort_key']);

        return array_slice($items, 0, $limit);
    }

    /**
     * @param array<string,mixed> $elementData
     * @return list<array<string,mixed>>
     */
    private static function fetchRepeating(array $elementData, int $limit, int $teaserLength, string $urlPattern, string $imageField = '', bool $showImage = false): array
    {
        $entryId = (int) ($elementData['repeat_entry'] ?? 0);
        if ($entryId <= 0) {
            return [];
        }

        // Faktory-API liefert pro Wiederholung einen Eintrag im Result-Array.
        // Wir holen alle Termine im Zeitraum und filtern auf die Entry-ID.
        $factory = \forCal\Factory\forCalEventsFactory::create()
            ->from('now')
            ->to('+24 months')
            ->sortBy('start_date', 'asc');

        /** @var list<array<string,mixed>> $entries */
        $entries = $factory->get();
        $items = [];

        foreach ($entries as $event) {
            $eid = (int) ($event['id'] ?? 0);
            if ($eid !== $entryId) {
                continue;
            }
            $item = self::makeItem($event, $teaserLength, $urlPattern, $imageField, $showImage);
            if (null === $item) {
                continue;
            }
            $items[] = $item;
            if (count($items) >= $limit) {
                break;
            }
        }

        return $items;
    }

    /**
     * @param array<string,mixed> $event
     * @return array<string,mixed>|null
     */
    private static function makeItem(array $event, int $teaserLength, string $urlPattern, string $imageField = '', bool $showImage = false): ?array
    {
        // forCalHandler::decorateEntry() liefert 'start' und 'end' bereits als
        // ISO-Strings (Y-m-d\TH:i:s bzw. Y-m-d bei Ganztag). Diese Werte parsen.
        $startStr = (string) ($event['start'] ?? '');
        $endStr = (string) ($event['end'] ?? $startStr);

        if ('' === $startStr) {
            // Fallback: aus start_date (ISO) + start_time (HH:MM:SS) zusammenbauen
            $rawDate = (string) ($event['start_date'] ?? '');
            $rawTime = (string) ($event['start_time'] ?? '');
            if ('' === $rawDate) {
                return null;
            }
            $startStr = '' !== $rawTime ? substr($rawDate, 0, 10) . ' ' . $rawTime : $rawDate;
            $endRawDate = (string) ($event['end_date'] ?? $rawDate);
            $endRawTime = (string) ($event['end_time'] ?? $rawTime);
            $endStr = '' !== $endRawTime ? substr($endRawDate, 0, 10) . ' ' . $endRawTime : $endRawDate;
        }

        try {
            $start = new DateTimeImmutable($startStr);
            $end = new DateTimeImmutable($endStr);
        } catch (Exception $e) {
            return null;
        }

        $id = (int) ($event['id'] ?? 0);
        $title = (string) ($event['title'] ?? '');
        $teaserRaw = (string) ($event['teaser'] ?? '');
        $teaser = self::truncate(strip_tags($teaserRaw), $teaserLength);
        $color = (string) ($event['color'] ?? ($event['category_color'] ?? ''));
        $venue = (string) ($event['venue_name'] ?? '');
        $startTime = (string) ($event['start_time'] ?? '');
        $endTime = (string) ($event['end_time'] ?? '');
        $fullTime = !empty($event['full_time'])
            || ('' === $startTime || '00:00:00' === $startTime);

        $href = '';
        if ('' !== $urlPattern && $id > 0) {
            $href = str_replace('{id}', (string) $id, $urlPattern);
        }

        // Bild aus konfiguriertem Feldnamen oder gaengigen Feldern ermitteln.
        // forCal praefixt eigene Felder bei der Ausgabe mit "entries_" – daher
        // pruefen wir sowohl <name> als auch entries_<name>.
        $image = '';
        $imageUrl = '';
        if ($showImage) {
            $clang = rex_clang::getCurrentId();
            $defaultCandidates = [
                'image',
                'lang_image_' . $clang,
                'bild',
                'header_image',
                'teaser_image',
                'media',
                'preview',
            ];
            $candidates = '' !== $imageField ? [$imageField] : $defaultCandidates;
            // Fuer jedes Candidate auch die "entries_"-Variante pruefen.
            $expanded = [];
            foreach ($candidates as $c) {
                $expanded[] = $c;
                if (!str_starts_with($c, 'entries_')) {
                    $expanded[] = 'entries_' . $c;
                }
            }
            foreach ($expanded as $key) {
                $val = $event[$key] ?? null;
                if (is_array($val)) {
                    $val = reset($val);
                }
                if (is_string($val) && '' !== trim($val)) {
                    $image = trim($val);
                    if (str_contains($image, ',')) {
                        $image = trim(explode(',', $image)[0]);
                    }
                    break;
                }
            }
            if ('' !== $image) {
                $imageUrl = rex_media_manager::getUrl('card', $image);
            }
        }

        return [
            'id' => $id,
            'title' => $title,
            'teaser' => $teaser,
            'start' => $start,
            'end' => $end,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'full_time' => $fullTime,
            'venue' => $venue,
            'category_color' => $color,
            'href' => $href,
            'image' => $image,
            'image_url' => $imageUrl,
            'sort_key' => $start->format('YmdHis'),
        ];
    }

    /**
     * Liefert eine kompakte Datums-Anzeige fuer Templates.
     *
     * @param array<string,mixed> $item
     */
    public static function formatDate(array $item): string
    {
        $start = $item['start'] ?? null;
        if (!$start instanceof DateTimeInterface) {
            return '';
        }
        $datePart = $start->format('d.m.Y');
        if (!empty($item['full_time'])) {
            return $datePart;
        }
        $startTime = (string) ($item['start_time'] ?? '');
        if ('' === $startTime) {
            return $datePart;
        }
        $startTime = substr($startTime, 0, 5);
        return $datePart . ' &middot; ' . $startTime . ' Uhr';
    }

    /**
     * @return array<int,string> id => Name
     */
    public static function getCategoryChoices(): array
    {
        if (!self::isAvailable()) {
            return [];
        }
        $clang = rex_clang::getCurrentId();
        $sql = rex_sql::factory();
        try {
            $rows = $sql->getArray(
                'SELECT id, name_' . $clang . ' AS name FROM ' . rex::getTable('forcal_categories')
                . ' WHERE status = 1 ORDER BY name_' . $clang . ' ASC',
            );
        } catch (Throwable $e) {
            return [];
        }
        $out = [];
        foreach ($rows as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            $id = (int) ($row['id'] ?? 0);
            if ($id <= 0 || '' === $name) {
                continue;
            }
            $out[$id] = $name;
        }
        return $out;
    }

    /**
     * @return array<int,string> id => Name (nur wiederkehrende, aktive Eintraege)
     */
    public static function getRepeatingEntryChoices(): array
    {
        if (!self::isAvailable()) {
            return [];
        }
        $clang = rex_clang::getCurrentId();
        $sql = rex_sql::factory();
        try {
            $rows = $sql->getArray(
                'SELECT id, name_' . $clang . ' AS name, start_date'
                . ' FROM ' . rex::getTable('forcal_entries')
                . ' WHERE type = :t AND status = 1'
                . ' ORDER BY name_' . $clang . ' ASC',
                [':t' => 'repeat'],
            );
        } catch (Throwable $e) {
            return [];
        }
        $out = [];
        foreach ($rows as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            $id = (int) ($row['id'] ?? 0);
            if ($id <= 0 || '' === $name) {
                continue;
            }
            $out[$id] = $name;
        }
        return $out;
    }

    public static function isAvailable(): bool
    {
        return rex_addon::get('forcal')->isAvailable()
            && class_exists(\forCal\Factory\forCalEventsFactory::class);
    }

    /**
     * @return list<int>
     */
    private static function parseCategoryIds(mixed $value): array
    {
        if (is_array($value)) {
            $raw = $value;
        } else {
            $raw = preg_split('/[\s,]+/', (string) $value) ?: [];
        }
        $out = [];
        foreach ($raw as $v) {
            $id = (int) $v;
            if ($id > 0 && !in_array($id, $out, true)) {
                $out[] = $id;
            }
        }
        return $out;
    }

    private static function truncate(string $text, int $length): string
    {
        $text = trim((string) preg_replace('/\s+/', ' ', $text));
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return rtrim(mb_substr($text, 0, $length - 1)) . '…';
    }

    /**
     * @return array{layout:string, items:list<array<string,mixed>>, error:string, limit:int}
     */
    private static function err(string $layout, string $msg): array
    {
        return [
            'layout' => $layout,
            'items' => [],
            'error' => $msg,
            'limit' => 0,
        ];
    }
}
