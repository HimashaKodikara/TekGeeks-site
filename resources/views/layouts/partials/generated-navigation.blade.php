<ul id="js-nav-menu" class="nav-menu">
    {{-- <li class="nav-title"><span>Insights</span></li> --}}

    @foreach ($menuItems as $item)
    @if(in_array($item->id,$arrParentID))
    @if($item->is_parent == 1 && $item->child_order == 1)
    <li>
        <a href="{{ url($item->url) }}">
            <i class="{{ $item->icon }}"></i>
            <span class="nav-link-text">{{ $item->title }}</span>
        </a>
    </li>
    @else
    <li class="nav-item">
        <a href="#" title="{{ $item->title }}" data-filter-tags>
            <i class="{{ $item->icon }}"></i>
            <span class="nav-link-text" data-i18n>{{ $item->title }}</span>
            {{-- <span class="badge bg-danger-700 badge-end">New</span> --}}
        </a>

        <ul>
            @foreach($subMenuItems as $subItem)
            @if($item->id == $subItem->parent_id)
            <li>
                <a href="{{ url($subItem->url) }}">
                    <span class="nav-link-text" data-i18n>{{ $subItem->title }}</span>
                </a>
            </li>
            @endif
            @endforeach
        </ul>
    </li>
    @endif
    @endif
    @endforeach

    {{-- <li class="nav-title"><span>Config</span></li>

    <li class="nav-title"><span>Layouts</span></li> --}}

    {{-- <li class="nav-item">
        <a href="#" title="Access Control" data-filter-tags>
            <svg class="sa-icon">
                <use href="/icons/sprite.svg#slash"></use>
            </svg>
            <span class="nav-link-text" data-i18n>Access Control</span>
        </a>

        <ul>
            <li class="nav-item">
                <a href="#" title="Authentication" data-filter-tags>
                    <span class="nav-link-text" data-i18n>Authentication Pages</span>
                </a>

                <ul>
                    <li>
                        <a href="{{ route('second', ['auth', 'login']) }}">
                            <span class="nav-link-text" data-i18n>Login</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('second', ['auth', 'register']) }}">
                            <span class="nav-link-text" data-i18n>Register</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('second', ['auth', 'forgetpassword']) }}">
                            <span class="nav-link-text" data-i18n>Forget Password</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('second', ['auth', 'twofactor']) }}">
                            <span class="nav-link-text" data-i18n>2FA</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('second', ['auth', 'lockscreen']) }}">
                            <span class="nav-link-text" data-i18n>Lock Screen</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('second', ['email', 'email-design']) }}">
                    <span class="nav-link-text" data-i18n>Email Templates</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" title="Error Pages" data-filter-tags>
                    <span class="nav-link-text" data-i18n>Error Pages</span>
                </a>

                <ul>
                    <li>
                        <a href="{{ route('second', ['error', '404']) }}">
                            <span class="nav-link-text" data-i18n>404 Not Found</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('second', ['error', '404-2']) }}">
                            <span class="nav-link-text" data-i18n>404 Not Found 2</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('second', ['error', '500']) }}">
                            <span class="nav-link-text" data-i18n>500 Internal Server</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('any', 'profile') }}">
                    <span class="nav-link-text" data-i18n>User Profile</span>
                </a>
            </li>
        </ul>
    </li> --}}

    {{-- <li class="nav-item">
        <a href="#" title="Communication" data-filter-tags>
            <svg class="sa-icon">
                <use href="/icons/sprite.svg#message-square"></use>
            </svg>
            <span class="nav-link-text" data-i18n>Communication</span>
        </a>

        <ul>
            <li>
                <a href="{{ route('any', 'messenger') }}">
                    <span class="nav-link-text" data-i18n>Messenger & Chat</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" title="Email" data-filter-tags>
                    <span class="nav-link-text" data-i18n>Email</span>
                </a>

                <ul>
                    <li>
                        <a href="{{ route('second', ['email', 'systemmail']) }}">
                            <span class="nav-link-text" data-i18n>System Mail</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('second', ['email', 'systemmail-read']) }}">
                            <span class="nav-link-text" data-i18n>Mail Read</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('any', 'usercontact') }}">
                    <span class="nav-link-text" data-i18n>User Contact</span>
                </a>
            </li>
        </ul>
    </li> --}}

    {{-- <li class="nav-item">
        <a href="#" title="Miscellaneous" data-filter-tags>
            <svg class="sa-icon">
                <use href="/icons/sprite.svg#archive"></use>
            </svg>
            <span class="nav-link-text" data-i18n>Miscellaneous</span>
        </a>

        <ul>
            <li>
                <a href="{{ route('second', ['forum', 'index']) }}">
                    <span class="nav-link-text" data-i18n>Forum General</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['forum', 'threads']) }}">
                    <span class="nav-link-text" data-i18n>Forum Threads</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['forum', 'discussion']) }}">
                    <span class="nav-link-text" data-i18n>Forum Discussions</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['forum', 'search']) }}">
                    <span class="nav-link-text" data-i18n>Search</span>
                </a>
            </li>
        </ul>
    </li> --}}

    {{-- <li>
        <a href="{{ route('any', 'landing') }}" target="_blank">
            <svg class="sa-icon">
                <use href="/icons/sprite.svg#zap"></use>
            </svg>
            <span class="nav-link-text">Landing</span>
        </a>
    </li> --}}

    {{-- <li class="nav-title"><span>Design</span></li> --}}

    {{-- <li class="nav-item">
        <a href="#" title="UI Components" data-filter-tags>
            <svg class="sa-icon">
                <use href="/icons/sprite.svg#layers"></use>
            </svg>
            <span class="nav-link-text" data-i18n>UI Components</span>
        </a>

        <ul>
            <li>
                <a href="{{ route('second', ['ui', 'alerts']) }}">
                    <span class="nav-link-text" data-i18n>Alerts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'accordions']) }}">
                    <span class="nav-link-text" data-i18n>Accordions</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'badges']) }}">
                    <span class="nav-link-text" data-i18n>Badges</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'buttons']) }}">
                    <span class="nav-link-text" data-i18n>Buttons</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'buttongroup']) }}">
                    <span class="nav-link-text" data-i18n>Button Group</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'cards']) }}">
                    <span class="nav-link-text" data-i18n>Cards</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'breadcrumbs']) }}">
                    <span class="nav-link-text" data-i18n>Breadcrumbs</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'dropdowns']) }}">
                    <span class="nav-link-text" data-i18n>Dropdowns</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'navbars']) }}">
                    <span class="nav-link-text" data-i18n>Navbars</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'pagination']) }}">
                    <span class="nav-link-text" data-i18n>Pagination</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'scrollspy']) }}">
                    <span class="nav-link-text" data-i18n>ScrollSpy</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'collapse']) }}">
                    <span class="nav-link-text" data-i18n>Collapse</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'modal']) }}">
                    <span class="nav-link-text" data-i18n>Modal</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'tabs-pills']) }}">
                    <span class="nav-link-text" data-i18n>Tabs & Pills</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'tooltips']) }}">
                    <span class="nav-link-text" data-i18n>Tooltips</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'popovers']) }}">
                    <span class="nav-link-text" data-i18n>Popovers</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'toasts']) }}">
                    <span class="nav-link-text" data-i18n>Toasts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'progressbars']) }}">
                    <span class="nav-link-text" data-i18n>Progress Bars</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'spinners']) }}">
                    <span class="nav-link-text" data-i18n>Spinners</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'carousels']) }}">
                    <span class="nav-link-text" data-i18n>Carousels</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'panels']) }}">
                    <span class="nav-link-text" data-i18n>Panels</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'list-filter']) }}">
                    <span class="nav-link-text" data-i18n>List Filter</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['ui', 'sidepanels']) }}">
                    <span class="nav-link-text" data-i18n>Side Panels</span>
                </a>
            </li>
        </ul>
    </li> --}}

    {{-- <li class="nav-item">
        <a href="#" title="System Utilities" data-filter-tags>
            <svg class="sa-icon">
                <use href="/icons/sprite.svg#package"></use>
            </svg>
            <span class="nav-link-text" data-i18n>System Utilities</span>
        </a>

        <ul>
            <li>
                <a href="{{ route('second', ['utilities', 'borders']) }}">
                    <span class="nav-link-text" data-i18n>Borders</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['utilities', 'display-property']) }}">
                    <span class="nav-link-text" data-i18n>Display Property</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['utilities', 'responsivegrid']) }}">
                    <span class="nav-link-text" data-i18n>Responsive Grid</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['utilities', 'position']) }}">
                    <span class="nav-link-text" data-i18n>Position</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['utilities', 'colorpalette']) }}">
                    <span class="nav-link-text" data-i18n>Color Palette</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['utilities', 'typography']) }}">
                    <span class="nav-link-text" data-i18n>Typography</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['utilities', 'sizing']) }}">
                    <span class="nav-link-text" data-i18n>Sizing</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['utilities', 'spacing']) }}">
                    <span class="nav-link-text" data-i18n>Spacing</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['utilities', 'flexbox']) }}">
                    <span class="nav-link-text" data-i18n>Flexbox</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['utilities', 'helpers']) }}">
                    <span class="nav-link-text" data-i18n>Helpers</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['utilities', 'visibility-generator']) }}">
                    <span class="nav-link-text" data-i18n>Visibility Generator</span>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item">
        <a href="#" title="Iconography" data-filter-tags>
            <svg class="sa-icon">
                <use href="/icons/sprite.svg#heart"></use>
            </svg>
            <span class="nav-link-text" data-i18n>Iconography</span>
        </a>

        <ul>
            <li>
                <a href="{{ route('second', ['icons', 'system']) }}">
                    <span class="nav-link-text" data-i18n>System Icons</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['icons', 'fontawesome']) }}">
                    <span class="nav-link-text" data-i18n>FontAwesome 5.3</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['icons', 'smartadmin']) }}">
                    <span class="nav-link-text" data-i18n>SmartAdmin Icons 1.0</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['icons', 'stackgenerator']) }}">
                    <span class="nav-link-text" data-i18n>Stack Generator</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['icons', 'stacklibrary']) }}">
                    <span class="nav-link-text" data-i18n>Stack Library</span>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item">
        <a href="#" title="Tables" data-filter-tags>
            <svg class="sa-icon">
                <use href="/icons/sprite.svg#table"></use>
            </svg>
            <span class="nav-link-text" data-i18n>Tables</span>
        </a>

        <ul>
            <li>
                <a href="{{ route('second', ['tables', 'basic']) }}">
                    <span class="nav-link-text" data-i18n>Basic Tables</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['tables', 'style-generator']) }}">
                    <span class="nav-link-text" data-i18n>Tables Style Generator</span>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item">
        <a href="#" title="Forms" data-filter-tags>
            <svg class="sa-icon">
                <use href="/icons/sprite.svg#edit"></use>
            </svg>
            <span class="nav-link-text" data-i18n>Forms</span>
        </a>

        <ul>
            <li>
                <a href="{{ route('second', ['forms', 'inputs']) }}">
                    <span class="nav-link-text" data-i18n>Inputs</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['forms', 'checkbox-radio']) }}">
                    <span class="nav-link-text" data-i18n>Checkbox & Radio</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['forms', 'groups']) }}">
                    <span class="nav-link-text" data-i18n>Groups</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['forms', 'validation']) }}">
                    <span class="nav-link-text" data-i18n>Validation</span>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-title"><span>Data Visualization</span></li>

    <li class="nav-item">
        <a href="#" title="Smart Tables" data-filter-tags>
            <svg class="sa-icon">
                <use href="/icons/sprite.svg#database"></use>
            </svg>
            <span class="nav-link-text" data-i18n>Smart Tables</span>
            <span class="badge bg-primary-500 badge-end">1.2.7</span>
        </a>

        <ul>
            <li>
                <a href="{{ route('second', ['smarttables', 'minimal'])}}">
                    <span class="nav-link-text" data-i18n>Minimal Settings</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['smarttables', 'responsive'])}}">
                    <span class="nav-link-text" data-i18n>Responsive Settings</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['smarttables', 'importexport-data'])}}">
                    <span class="nav-link-text" data-i18n>Import & Export Data</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['smarttables', 'json-source'])}}">
                    <span class="nav-link-text" data-i18n>JSON Data Source</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['smarttables', 'manage-records'])}}">
                    <span class="nav-link-text" data-i18n>Manage Records</span>
                    <span class="badge bg-warning text-dark badge-end">New</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['smarttables', 'fuzzy-matching'])}}">
                    <span class="nav-link-text" data-i18n>Fuzzy Matching</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['smarttables', 'server-side'])}}">
                    <span class="nav-link-text" data-i18n>Server-Side Mode</span>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item">
        <a href="#" title="Apex Charts" data-filter-tags>
            <svg class="sa-icon">
                <use href="/icons/sprite.svg#pie-chart"></use>
            </svg>
            <span class="nav-link-text" data-i18n>Apex Charts</span>
        </a>

        <ul>
            <li>
                <a href="{{ route('second', ['apex', 'area'])}}">
                    <span class="nav-link-text" data-i18n>Area Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'bar'])}}">
                    <span class="nav-link-text" data-i18n>Bar Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'box-whisker'])}}">
                    <span class="nav-link-text" data-i18n>Box & Whisker Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'bubble'])}}">
                    <span class="nav-link-text" data-i18n>Bubble Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'candlestick'])}}">
                    <span class="nav-link-text" data-i18n>Candlestick Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'column'])}}">
                    <span class="nav-link-text" data-i18n>Column Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'funnel'])}}">
                    <span class="nav-link-text" data-i18n>Funnel Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'heatmap'])}}">
                    <span class="nav-link-text" data-i18n>Heatmap Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'line'])}}">
                    <span class="nav-link-text" data-i18n>Line Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'mixed-combo'])}}">
                    <span class="nav-link-text" data-i18n>Mixed/Combo Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'pie-donut'])}}">
                    <span class="nav-link-text" data-i18n>Pie/Donuts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'polar-area'])}}">
                    <span class="nav-link-text" data-i18n>Polar Area Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'radar'])}}">
                    <span class="nav-link-text" data-i18n>Radar Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'radialbars-circle'])}}">
                    <span class="nav-link-text" data-i18n>RadialBars/Circle Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'range-area'])}}">
                    <span class="nav-link-text" data-i18n>Range Area Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'scatter'])}}">
                    <span class="nav-link-text" data-i18n>Scatter Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'slope'])}}">
                    <span class="nav-link-text" data-i18n>Slope Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'sparkline'])}}">
                    <span class="nav-link-text" data-i18n>Sparklines</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'timeline'])}}">
                    <span class="nav-link-text" data-i18n>Timeline Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['apex', 'treemap'])}}">
                    <span class="nav-link-text" data-i18n>Treemap Charts</span>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item">
        <a href="#" title="Data Bites" data-filter-tags>
            <svg class="sa-icon">
                <use href="/icons/sprite.svg#grid"></use>
            </svg>
            <span class="nav-link-text" data-i18n>Data Bites</span>
        </a>

        <ul>
            <li>
                <a href="{{ route('second', ['databites', 'peity-charts']) }}">
                    <span class="nav-link-text" data-i18n>Peity Charts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['databites', 'streamline']) }}">
                    <span class="nav-link-text" data-i18n>Streamline</span>
                </a>
            </li>
            <li>
                <a href="{{ route('second', ['databites', 'easy-pie-chart'])}}">
                    <span class="nav-link-text" data-i18n>Easy Pie Chart</span>
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="{{ route('any', 'fullcalendar')}}">
            <svg class="sa-icon">
                <use href="/icons/sprite.svg#calendar"></use>
            </svg>
            <span class="nav-link-text">Full Calendar</span>
        </a>
    </li> --}}
</ul>
