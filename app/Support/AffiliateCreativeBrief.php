<?php

namespace App\Support;

class AffiliateCreativeBrief
{
    public static function forVariation(string $mode, int $index): array
    {
        $packs = self::modePacks();
        $pack = $packs[$mode] ?? $packs['fast_test'];

        return [
            'format' => self::pick($pack['formats'], $index),
            'angle' => self::pick($pack['angles'], $index),
            'hook' => self::pick($pack['hooks'], $index),
            'structure' => $pack['structure'],
            'mode_label' => $pack['label'],
            'goal' => $pack['goal'],
            'visual_style' => $pack['visual_style'],
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

    public static function modeInstruction(string $mode): string
    {
        $packs = self::modePacks();
        $pack = $packs[$mode] ?? $packs['fast_test'];

        return implode("\n", [
            "Mode {$pack['label']}: {$pack['goal']}",
            "Format nen dung: " . implode(' / ', $pack['formats']),
            "Khung noi dung: {$pack['structure']}",
            "Nhip video: {$pack['pace']}",
        ]);
    }

    private static function pick(array $items, int $index): string
    {
        return $items[$index % count($items)];
    }

    private static function modePacks(): array
    {
        return [
            'fast_test' => [
                'label' => 'Fast Test',
                'goal' => 'test nhanh hook va goc ban, uu tien giu chan 3 giay dau va tao nhieu bien the de tim winner',
                'formats' => [
                    'Stop Scroll Problem',
                    'Do Not Buy Before Watching',
                    'Tiny Fix Big Relief',
                    'I Wish I Knew This Earlier',
                    'Common Annoyance Fix',
                ],
                'angles' => [
                    'dap thang vao mot bat tien lap lai moi ngay cua nguoi mua',
                    'noi ro ai nen can nhac san pham nay va ai khong can',
                    'bien mot van de nho nhung kho chiu thanh cach xu ly nhanh hon',
                    'cho nguoi xem thay ly do nen bam gio hang de kiem tra them',
                    'tao cam giac mon nay thuc dung, khong mau me nhung dang thu',
                ],
                'hooks' => [
                    'Dung luot qua neu ban dang gap cai nay moi ngay.',
                    'Mon nay khong than ky, nhung no giai quyet dung mot viec rat phien.',
                    'Truoc khi mua mon nay, xem nhanh 15 giay nay da.',
                    'Neu nha ban co van de nay, cai nay dang de xem thu.',
                    'Toi tuong no vo dung, nhung dung roi moi thay tien.',
                ],
                'structure' => 'hook cuc ngan, goi dung noi dau, dua 2 loi ich cu the, chen mot cau chong noi qua, CTA gio hang',
                'pace' => 'nhanh, cau ngan, it tinh tu, moi 3-4 giay co mot y moi',
                'visual_style' => 'raw product review, close-up details, quick before-after feel, practical daily use',
            ],
            'premium_product' => [
                'label' => 'Premium Product',
                'goal' => 'lam san pham trong dang tin va dang tien hon, phu hop khi anh san pham dep hoac can visual lifestyle',
                'formats' => [
                    'Premium Daily Upgrade',
                    'Room Or Routine Upgrade',
                    'Clean Lifestyle Review',
                    'Why It Feels Worth It',
                    'Giftable Find',
                ],
                'angles' => [
                    'nhan manh cam giac gon dep, sach se, de dung trong doi song hang ngay',
                    'dat san pham vao mot ngu canh su dung co gu va dang tin',
                    'tap trung vao chat lieu, thiet ke, cam giac tien loi neu thong tin nguoi dung co cung cap',
                    'bien san pham thanh mot nang cap nho cho khong gian hoac thoi quen',
                    'lam nguoi xem nghi den viec mua cho ban than hoac lam qua tang',
                ],
                'hooks' => [
                    'Neu ban thich do vua dep vua co ich, xem mon nay.',
                    'Mot nang cap nho nhung lam goc nay gon hon han.',
                    'Mon nay hop voi ai thich nha cua gon va de dung.',
                    'Khong can qua cau ky, chi can dung mon dung viec.',
                    'Nhin ngoai doi se de hieu vi sao nhieu nguoi them vao gio hang.',
                ],
                'structure' => 'hook mem, ngu canh su dung, 3 diem lam san pham dang tien, cau review that, CTA nhe',
                'pace' => 'vua phai, premium nhung khong xa cach, cau co hinh anh de khop visual lifestyle',
                'visual_style' => 'clean lifestyle product ad, tasteful close-ups, warm practical scene, premium but realistic',
            ],
            'winner_scale' => [
                'label' => 'Winner Scale',
                'goal' => 'nhan ban goc ban co kha nang ra don, tap trung conversion va su ro rang cua ly do mua',
                'formats' => [
                    'Three Reasons To Add To Cart',
                    'Before After Use Case',
                    'Buyer Objection Answer',
                    'Who This Is For',
                    'Problem Proof CTA',
                ],
                'angles' => [
                    'lap lai core pain point va chot bang ly do bam gio hang ngay',
                    'tra loi nghi ngo lon nhat cua nguoi mua bang ngon ngu an toan',
                    'nhan manh 3 ly do thuc dung de them vao gio hang',
                    'noi ro nhom nguoi nen mua de tang do lien quan',
                    'dua nguoi xem tu van de sang hanh dong kiem tra san pham',
                ],
                'hooks' => [
                    'Neu ban dang phan van co nen mua khong, nghe 3 y nay.',
                    'Day la ly do mon nay dang de nam trong gio hang.',
                    'Khong phai ai cung can, nhung neu ban thuoc nhom nay thi nen xem.',
                    'Van de cua mon nay khong nam o quang cao, ma nam o viec no co dung viec cua ban khong.',
                    'Xem xong 20 giay nay roi quyet dinh co bam gio hang khong.',
                ],
                'structure' => 'hook phan loai nguoi mua, 3 ly do cu the, xu ly 1 objection, CTA ro nhung khong ep',
                'pace' => 'chac, ro y, moi cau phai phuc vu quyet dinh mua',
                'visual_style' => 'conversion focused product proof, close-up, benefit sequence, clear use case, no fake claims',
            ],
        ];
    }
}
