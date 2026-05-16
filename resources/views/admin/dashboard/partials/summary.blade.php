        <section class="summary-grid admin-dashboard-summary">
            @foreach ($stats as $stat)
                <article class="summary-card summary-card--{{ $stat['tone'] }}">
                    <p class="summary-card__label">{{ $stat['label'] }}</p>
                    <div class="summary-card__value">
                        <strong>
                            @if(!empty($stat['currency']))
                                &#8369; {{ number_format((float) $stat['value'], 2) }}
                            @else
                                {{ $stat['value'] }}
                            @endif
                        </strong>
                        <span>{{ $stat['note'] }}</span>
                    </div>
                </article>
            @endforeach
        </section>
