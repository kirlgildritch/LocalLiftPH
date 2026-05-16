                <article class="panel-card">
                    <div class="section-card__header">
                        <h3 class="section-title">Recent Activity</h3>
                    </div>

                    @if ($recentActivity->isEmpty())
                        <div class="dashboard-empty sub-line">No recent activity.</div>
                    @else
                        <div class="dashboard-activity-list">
                            @foreach ($recentActivity as $activity)
                                <article class="activity-item">
                                    <div class="activity-item__top">
                                        <span class="activity-item__type activity-item__type--{{ $activity['tone'] }}">{{ $activity['type'] }}</span>
                                        <span class="dashboard-inline-note">{{ optional($activity['time'])->diffForHumans() }}</span>
                                    </div>
                                    <div>
                                        <div class="activity-item__title">{{ $activity['title'] }}</div>
                                        <div class="activity-item__meta">{{ $activity['meta'] }}</div>
                                    </div>
                                    <div class="activity-item__actions">
                                        <a href="{{ $activity['action_url'] }}" class="action-button action-button--primary">{{ $activity['action_label'] }}</a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </article>
