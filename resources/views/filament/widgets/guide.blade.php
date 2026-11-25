{{-- ダッシュボードの「使い方」。Vite / Tailwind を入れていないので、レイアウトは Filament の部品とインラインの style で組む --}}
<x-filament-widgets::widget>
    <x-filament::section
        heading="使い方"
        description="左のメニューの「＋ 登録する」から登録、「一覧を見る」から一覧へ。下のボタンからも進めます。"
    >
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @foreach ($this->getSteps() as $index => $step)
                <div>
                    <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.5rem; height: 1.5rem; border-radius: 9999px; background: #e9524e; color: #fff; font-size: 0.8rem; font-weight: 700; flex: none;">
                            {{ $index + 1 }}
                        </span>
                        <h3 style="font-weight: 700; font-size: 1rem;">{{ $step['title'] }}</h3>
                    </div>
                    <p style="font-size: 0.875rem; opacity: 0.75; margin: 0 0 0.75rem 2rem;">{{ $step['description'] }}</p>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(15rem, 1fr)); gap: 0.75rem; margin-left: 2rem;">
                        @foreach ($step['resources'] as $card)
                            <div style="border: 1px solid rgba(127, 127, 127, 0.25); border-radius: 0.75rem; padding: 0.875rem 1rem; display: flex; flex-direction: column; gap: 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <x-filament::icon :icon="$card['icon']" style="width: 1.25rem; height: 1.25rem; color: #e9524e;" />
                                    <span style="font-weight: 700;">{{ $card['label'] }}</span>
                                    <span style="margin-left: auto; font-size: 0.8rem; opacity: 0.75; white-space: nowrap;">
                                        {{ $card['count'] }}
                                    </span>
                                </div>
                                <p style="font-size: 0.8rem; opacity: 0.75; flex: 1;">{{ $card['description'] }}</p>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <x-filament::button tag="a" :href="$card['listUrl']" size="sm" color="gray" outlined>
                                        一覧を見る
                                    </x-filament::button>
                                    <x-filament::button tag="a" :href="$card['createUrl']" size="sm" icon="heroicon-m-plus">
                                        登録する
                                    </x-filament::button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
