                <article class="panel-card dashboard-overview-card dashboard-overview-card--users">
                    <div class="section-card__header">
                        <h3 class="section-title">Sales Overview</h3>
                    </div>

                    <div class="dashboard-mini-grid">
                        @foreach ($salesOverview as $metric)
                            <article class="dashboard-mini-card dashboard-mini-card--{{ $metric['tone'] }}">
                                <span>{{ $metric['label'] }}</span>
                                <strong>
                                    @if($metric['currency'])
                                        &#8369; {{ number_format((float) $metric['value'], 2) }}
                                    @else
                                        {{ $metric['value'] }}
                                    @endif
                                </strong>
                                <small>{{ $metric['note'] }}</small>
                            </article>
                        @endforeach
                    </div>
                </article>
