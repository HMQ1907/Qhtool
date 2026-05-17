<?php

namespace App\Support;

class AffiliateCreativeBrief
{
    public static function forVariation(string $mode, int $index): array
    {
        $formats = [
            'Problem Solver',
            'Underpriced Find',
            'Before After',
            'Three Reasons',
            'Do Not Buy Before Watching',
            'Small Item Big Convenience',
            'Room Upgrade',
            'Daily Use Test',
        ];

        $angles = [
            'giai quyet mot bat tien nho nhung lap lai moi ngay',
            'mon do gia mem nhung lam viec nha nhe hon',
            'san pham phu hop phong tro, can ho nho hoac nguoi ban ron',
            'tap trung vao su tien loi, gon gang va de dung',
            'so sanh truoc va sau khi co san pham',
            'nhan manh ly do nen xem gia trong gio hang hom nay',
            'bien mot viec vat vat thanh viec lam nhanh hon',
            'phu hop de mua thu neu gia va danh gia tot',
        ];

        $hooks = [
            'Mon nay nho nhung dung moi ngay moi thay tien.',
            'Dung mua mon nay neu ban chua xem het video nay.',
            'Neu nha ban dang bi van de nay, xem thu cai nay.',
            'Duoi muc gia nay ma tien vay thi dang de can nhac.',
            'Toi nghi nhieu nguoi se can mon nay hon ho tuong.',
            'Mot mon do nho giup goc nha gon hon kha nhieu.',
            'Thu nay khong than ky, nhung no giai quyet dung van de.',
            'Neu ban thich do tien ich, mon nay nen nam trong gio hang.',
        ];

        return [
            'format' => $formats[$index % count($formats)],
            'angle' => $angles[$index % count($angles)],
            'hook' => $hooks[$index % count($hooks)],
            'mode_label' => match ($mode) {
                'premium_product' => 'Premium Product',
                'winner_scale' => 'Winner Scale',
                default => 'Fast Test',
            },
        ];
    }

    public static function hashtags(): array
    {
        return [
            '#tiktokshop',
            '#reviewdo',
            '#dohay',
            '#giadungthongminh',
            '#tienichgiadinh',
            '#muasamthongminh',
            '#reviewthat',
        ];
    }
}
