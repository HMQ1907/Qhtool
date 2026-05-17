<?php

namespace App\Support;

use App\Models\CampaignVideo;

class ShortVideoCreativeBrief
{
    public static function for(CampaignVideo $video): array
    {
        $seed = (int) ($video->id ?: crc32((string) $video->title));

        return [
            'series' => self::pick(self::series(), $seed),
            'angle' => self::pick(self::angles(), $seed + 3),
            'hook_style' => self::pick(self::hookStyles(), $seed + 7),
            'visual_world' => self::pick(self::visualWorlds(), $seed + 11),
            'symbol' => self::pick(self::symbols(), $seed + 17),
            'motion' => self::pick(self::motions(), $seed + 23),
            'palette' => self::pick(self::palettes(), $seed + 29),
            'retention_pattern' => self::pick(self::retentionPatterns(), $seed + 31),
            'hashtags' => self::pick(self::hashtagSets(), $seed + 37),
        ];
    }

    private static function pick(array $items, int $seed): mixed
    {
        return $items[abs($seed) % count($items)];
    }

    private static function series(): array
    {
        return [
            'Stoic Rule',
            'Dark Psychology Signal',
            'Emotional Control Lesson',
            'Silent Power Principle',
            'Modern Marcus Aurelius',
            'Discipline Over Dopamine',
            'Social Status Trap',
            'The Calm Mind Code',
        ];
    }

    private static function angles(): array
    {
        return [
            'why chasing validation quietly destroys self-respect',
            'how silence becomes power when someone tries to provoke you',
            'the psychological cost of explaining yourself to everyone',
            'why disciplined people look boring before they become unstoppable',
            'how emotionally intelligent people spot manipulation early',
            'the Stoic way to stop reacting to disrespect',
            'why comfort addiction makes men and women easier to control',
            'how to detach without becoming cold or bitter',
            'why people test your boundaries after you start changing',
            'the hidden danger of needing closure from someone who hurt you',
            'how envy reveals what you secretly believe you cannot have',
            'why your attention is the most expensive thing you own',
        ];
    }

    private static function hookStyles(): array
    {
        return [
            'Start with a sharp warning that feels personal.',
            'Start by challenging a common self-help belief.',
            'Start with a quiet sentence that sounds like a hard truth.',
            'Start with a direct command to stop one damaging habit.',
            'Start with a paradox that makes the viewer wait for the answer.',
            'Start with a social observation that feels painfully accurate.',
        ];
    }

    private static function visualWorlds(): array
    {
        return [
            'rain-soaked marble corridor, cracked Roman busts, candlelight, solitary figure',
            'ancient library at night, dust in god rays, handwritten notes, bronze statue shadows',
            'stormy coastline turning calm, lone silhouette on black rocks, distant temple ruins',
            'minimal stone room, hourglass, old mirror, slow smoke, one chair under hard light',
            'empty city street before sunrise, classical statue fragments, wet pavement reflections',
            'dark courtroom-like hall, columns, falling ash, stern philosopher statue closeups',
            'misty mountain path, broken chains on stone, warm sunrise cutting through cold fog',
            'quiet battlefield after rain, abandoned helmet, olive branch, calm sky opening',
        ];
    }

    private static function symbols(): array
    {
        return [
            'hourglass losing sand',
            'cracked marble face',
            'black chess king tipped over',
            'old mirror with no reflection',
            'broken chain on stone',
            'single candle almost going out',
            'closed iron gate opening slowly',
            'storm clouds parting over a statue',
        ];
    }

    private static function motions(): array
    {
        return [
            'slow dolly-in, controlled parallax, subtle film grain',
            'gentle orbit shot, slow push through atmospheric haze',
            'wide establishing shot into tight symbolic closeups',
            'slow handheld drift, cinematic rack focus, calm pacing',
            'low angle tracking shot, gradual light reveal, no fast cuts',
            'macro detail shots blended with lonely wide frames',
        ];
    }

    private static function palettes(): array
    {
        return [
            'charcoal, marble white, muted gold, cold rain blue',
            'deep black, bronze, ivory paper, candle amber',
            'storm gray, sea green, pale sunrise gold, stone white',
            'graphite, faded burgundy, smoke gray, warm skin highlights',
            'wet asphalt black, silver, muted olive, soft dawn orange',
            'obsidian, ash gray, antique brass, restrained crimson',
        ];
    }

    private static function retentionPatterns(): array
    {
        return [
            'hook, reversal, practical rule, final loop',
            'pain point, hidden cause, Stoic reframe, one action',
            'social trigger, psychological explanation, boundary rule, follow-up question',
            'myth, uncomfortable truth, three short commands, save-worthy line',
            'pattern interrupt, example, internal shift, memorable closing sentence',
        ];
    }

    private static function hashtagSets(): array
    {
        return [
            ['#stoicism', '#psychology', '#mindset', '#selfmastery', '#shorts'],
            ['#darkpsychology', '#stoicmindset', '#discipline', '#mentalstrength', '#reels'],
            ['#marcusaurelius', '#psychologyfacts', '#emotionalcontrol', '#wisdom', '#shorts'],
            ['#stoicquotes', '#selfimprovement', '#boundaries', '#mindsetshift', '#reels'],
            ['#discipline', '#stoicism', '#darkpsychology', '#motivation', '#shorts'],
        ];
    }
}
