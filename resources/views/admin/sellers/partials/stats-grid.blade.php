<section class="seller-stats-grid">
    @foreach ($stats as $stat)
        <article class="seller-stat-card seller-stat-card--{{ $stat['tone'] }}">
            <p>{{ $stat['label'] }}</p>
            <strong>{{ $stat['value'] }}</strong>
        </article>
    @endforeach
</section>
