                <article class="panel-card dashboard-overview-card">
                    <div class="section-card__header">
                        <h3 class="section-title">User Management</h3>
                    </div>

                    <div class="dashboard-mini-grid">
                        @foreach ($userManagement as $metric)
                            <article class="dashboard-mini-card dashboard-mini-card--{{ $metric['tone'] }}">
                                <span>{{ $metric['label'] }}</span>
                                <strong>{{ $metric['value'] }}</strong>
                                <small>Marketplace users</small>
                            </article>
                        @endforeach
                    </div>
                </article>
