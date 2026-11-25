<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

/**
 * 特集。本体の backend/features.go の featureDefs から移したもの。
 *
 * 「一人で行ける神社と一杯」は、一人でも入りやすいお店（お店の solo_friendly）が
 * 紐づいた神社を並べる特集。仮置きのお店を移さないので、本物のお店が入るまで非公開にする。
 */
class FeatureSeeder extends Seeder
{
    /** @var list<array<string, mixed>> */
    private const array FEATURES = [
        [
            'slug' => 'career', 'title' => '仕事でモヤモヤした日に行きたい神社%d選', 'is_numbered' => true,
            'summary' => '参拝のあとに寄りたい蕎麦屋と酒場も',
            'lead' => '上司のこと、評価のこと、この先のこと。仕事でモヤモヤが続く日は、気持ちを切り替えに神社へ。仕事運や出世を願う神社と、参拝のあとに寄りたいお店をまとめました。',
            'conditions' => ['worry' => 'career'], 'is_published' => true,
        ],
        [
            'slug' => 'kagurazaka-night', 'title' => '縁結び神社と、神楽坂の夜', 'is_numbered' => false,
            'summary' => '良縁祈願のあとは小料理屋で日本酒',
            'lead' => '神楽坂の近くにある神社にお参りしたら、そのまま神楽坂の路地へ。小料理屋や和食店で日本酒をゆっくり楽しむ、夜の過ごし方をまとめました。',
            'conditions' => ['q' => '神楽坂'], 'is_published' => true,
        ],
        [
            'slug' => 'yakuyoke-kanpai', 'title' => '厄を祓って、酒場で乾杯', 'is_numbered' => false,
            'summary' => '厄除け神社と、帰りに寄れる居酒屋',
            'lead' => '悪いことが続いた日や、厄年で落ち着かないときは、厄除けの神社へ。お参りで気持ちを切り替えたら、帰りに酒場で乾杯しましょう。',
            'conditions' => ['q' => '厄除け'], 'is_published' => true,
        ],
        [
            'slug' => 'solo', 'title' => '一人で行ける神社と一杯', 'is_numbered' => false,
            'summary' => 'カウンターで気軽に飲めるお店',
            'lead' => '一人で神社に行って、一人で一杯。一人でも入りやすいお店が近くにある神社を集めました。',
            'conditions' => ['k' => ['solo']], 'is_published' => false,
        ],
    ];

    public function run(): void
    {
        foreach (self::FEATURES as $order => $data) {
            Feature::query()->updateOrCreate(
                ['slug' => $data['slug']],
                [...$data, 'sort_order' => $order],
            );
        }
    }
}
