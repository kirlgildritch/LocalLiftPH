<article class="table-card">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Reported Item</th>
                    <th>Reason</th>
                    <th>Reporter</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $report)
                    @include('admin.reports.partials.table-row', [
                        'report' => $report,
                        'reportStatusClass' => $reportStatusClass,
                    ])
                @empty
                    <tr>
                        <td colspan="6" class="empty-text">No reports submitted yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</article>
