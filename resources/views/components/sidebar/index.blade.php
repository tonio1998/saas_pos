<div class="sidebar d-flex flex-column">

    <x-sidebar.logo />

    <div class="sidebar-scroll flex-grow-1 py-3">

        @foreach(config('sidebar') as $section)

            <x-sidebar.section :title="$section['title']">

                @foreach($section['items'] as $item)

                    @if($item['type'] === 'link')

                        <x-sidebar.item
                            :route="$item['route']"
                            :icon="$item['icon']"
                            :active="request()->routeIs($item['active'])"
                        >
                            {{ $item['label'] }}
                        </x-sidebar.item>

                    @elseif($item['type'] === 'collapse')

                        @php
                            $expanded = false;

                            foreach ($item['active'] as $route) {
                                if (request()->routeIs($route)) {
                                    $expanded = true;
                                    break;
                                }
                            }
                        @endphp

                        <x-sidebar.collapse
                            :id="$item['id']"
                            :title="$item['label']"
                            :icon="$item['icon']"
                            :expanded="$expanded"
                        >

                            @foreach($item['children'] as $child)

                                <a
                                    href="{{ route($child['route']) }}"
                                    class="sidebar-sublink {{ request()->routeIs($child['active']) ? 'active' : '' }}"
                                >

                                    <i class="{{ $child['icon'] }} sidebar-subicon"></i>

                                    <span>{{ $child['label'] }}</span>

                                </a>

                            @endforeach

                        </x-sidebar.collapse>

                    @endif

                @endforeach

            </x-sidebar.section>

        @endforeach

    </div>

</div>
