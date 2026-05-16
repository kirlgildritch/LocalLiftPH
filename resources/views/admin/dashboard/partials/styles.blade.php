    <style>
        .dashboard-section-grid,
        .dashboard-mini-grid {
            display: grid;
            gap: 1rem;
        }

        .dashboard-section-grid {
            grid-template-columns: 1.5fr 1fr;
            align-items: start;
        }

        .dashboard-mini-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            padding: 1rem 1.25rem 1.25rem;
            align-items: stretch;
        }

        .dashboard-mini-card {
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1rem;
            background: var(--surface-soft);
            display: grid;
            gap: 0.45rem;
            height: 100%;
            align-content: start;
        }

        .dashboard-mini-card strong {
            font-size: 1.65rem;
            line-height: 1;
            color: var(--text);
        }

        .dashboard-mini-card span,
        .dashboard-mini-card small,
        .activity-item__meta,
        .dashboard-inline-note {
            color: var(--muted);
        }

        .dashboard-mini-card small {
            font-size: 0.86rem;
        }

        .dashboard-mini-card--primary strong {
            color: var(--primary);
        }

        .dashboard-mini-card--success strong {
            color: var(--success);
        }

        .dashboard-mini-card--warning strong {
            color: #f39d12;
        }

        .dashboard-mini-card--danger strong {
            color: var(--danger);
        }

        .dashboard-activity-list,
        .dashboard-shop-list {
            display: grid;
            gap: 0.9rem;
            padding: 1rem 1.25rem 1.25rem;
        }

        .activity-item,
        .shop-verify-card {
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1rem;
            background: var(--surface);
        }

        .activity-item {
            display: grid;
            gap: 0.7rem;
        }

        .activity-item__top,
        .shop-verify-card__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .activity-item__type {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 0.35rem 0.7rem;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .activity-item__type--primary {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .activity-item__type--success {
            background: var(--success-soft);
            color: var(--success);
        }

        .activity-item__type--warning {
            background: var(--warning-soft);
            color: #a27816;
        }

        .activity-item__type--danger {
            background: var(--danger-soft);
            color: var(--danger);
        }

        .activity-item__title,
        .shop-verify-card__title,
        .dashboard-table-name {
            font-weight: 700;
            color: #30405e;
        }

        .activity-item__actions,
        .shop-verify-card__actions,
        .dashboard-inline-actions {
            display: flex;
            gap: 0.65rem;
            flex-wrap: wrap;
        }

        .dashboard-inline-actions form,
        .shop-verify-card__actions form {
            margin: 0;
        }

        .dashboard-panel-table {
            padding: 0 1.25rem 1.25rem;
        }

        .dashboard-panel-table .data-table {
            min-width: 0;
        }

        .dashboard-panel-table .data-table th,
        .dashboard-panel-table .data-table td {
            padding-left: 0.85rem;
            padding-right: 0.85rem;
        }

        .dashboard-product-meta {
            display: grid;
            gap: 0.25rem;
        }

        .dashboard-empty {
            padding: 1rem 1.25rem 1.25rem;
        }

        .shop-verify-card__meta {
            display: grid;
            gap: 0.45rem;
            color: var(--muted);
            font-size: 0.94rem;
        }

        .admin-dashboard-summary .summary-card,
        .dashboard-overview-card {
            height: 100%;
        }

        @media (max-width: 1200px) {
            .dashboard-mini-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-section-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 1024px) and (max-width: 1440px) {
            .page {
                padding: 1.35rem;
            }

            .page-stack {
                gap: 1.25rem;
            }

            .admin-dashboard-summary {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 0.9rem;
            }

            .admin-dashboard-summary .summary-card {
                min-height: 112px;
                padding: 0.95rem 1.05rem;
                display: grid;
                align-content: space-between;
            }

            .admin-dashboard-summary .summary-card__label {
                margin-bottom: 0.4rem;
                font-size: 0.88rem;
                line-height: 1.35;
            }

            .admin-dashboard-summary .summary-card__value {
                display: grid;
                gap: 0.35rem;
                align-items: start;
            }

            .admin-dashboard-summary .summary-card__value strong {
                font-size: clamp(2rem, 1.8vw, 2.45rem);
                line-height: 0.96;
                overflow-wrap: anywhere;
            }

            .admin-dashboard-summary .summary-card__value span {
                padding-bottom: 0;
                font-size: 0.86rem;
                line-height: 1.35;
            }

            .admin-dashboard-sections {
                grid-template-columns: minmax(0, 1.22fr) minmax(0, 1.02fr);
                gap: 1rem;
            }

            .admin-dashboard-sections > .stack {
                gap: 1rem;
            }

            .dashboard-overview-card {
                display: grid;
                grid-template-rows: auto 1fr;
            }

            .dashboard-overview-card .section-card__header {
                padding: 0.95rem 1.1rem;
            }

            .dashboard-overview-card .dashboard-mini-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 0.85rem;
                padding: 1rem 1.1rem 1.1rem;
            }

            .dashboard-overview-card .dashboard-mini-card {
                min-height: 148px;
                padding: 0.95rem 0.85rem;
                gap: 0.4rem;
                align-content: space-between;
            }

            .dashboard-overview-card .dashboard-mini-card span {
                font-size: 0.84rem;
                line-height: 1.28;
                text-wrap: balance;
            }

            .dashboard-overview-card .dashboard-mini-card strong {
                font-size: clamp(1.7rem, 1.65vw, 2.05rem);
                line-height: 0.98;
                overflow-wrap: anywhere;
            }

            .dashboard-overview-card .dashboard-mini-card small {
                font-size: 0.78rem;
                line-height: 1.28;
            }
        }

        @media (max-width: 760px) {
            .dashboard-mini-grid {
                grid-template-columns: 1fr;
            }

            .activity-item__top,
            .shop-verify-card__top {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
