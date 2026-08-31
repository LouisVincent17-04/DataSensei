@once
<style id="datasensei-admin-inspired-page-style">
  /*
   * Shared student/instructor page treatment based on the admin workspace:
   * flat surfaces, compact controls, restrained headings, and 8px panels.
   */
  body.ds-admin-inspired {
    background-color: var(--bg, #0d1320) !important;
    background-image: none !important;
    color: var(--text, #fafafa);
  }

  .ds-admin-inspired .page-kicker,
  .ds-admin-inspired .eyebrow {
    display: block;
    margin-bottom: 6px;
    color: var(--accent, #3b82f6);
    font-size: .7rem;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
  }

  .ds-admin-inspired .page-kicker::before,
  .ds-admin-inspired .eyebrow::before,
  .ds-admin-inspired .section-title::before,
  .ds-admin-inspired .stat::after,
  .ds-admin-inspired .mission::before,
  .ds-admin-inspired .achievement-card::before,
  .ds-admin-inspired .achievement-card::after {
    display: none !important;
  }

  .ds-admin-inspired .page-title,
  .ds-admin-inspired .title,
  .ds-admin-inspired h1.ds-page-title {
    margin-top: 0;
    color: var(--text, #fafafa);
    font-size: clamp(1.35rem, 2.2vw, 1.65rem) !important;
    font-weight: 700 !important;
    letter-spacing: -.025em !important;
    line-height: 1.2 !important;
  }

  .ds-admin-inspired .page-subtitle,
  .ds-admin-inspired .subtitle {
    margin-top: 7px;
    color: var(--muted, #7f93b0);
    font-size: .875rem;
    line-height: 1.55;
  }

  .ds-admin-inspired .top-row,
  .ds-admin-inspired .header,
  .ds-admin-inspired .hero {
    align-items: flex-start;
    margin-bottom: 24px;
  }

  .ds-admin-inspired .card,
  .ds-admin-inspired .panel,
  .ds-admin-inspired .stat,
  .ds-admin-inspired .stat-card,
  .ds-admin-inspired .metric,
  .ds-admin-inspired .mission,
  .ds-admin-inspired .achievement-card,
  .ds-admin-inspired .library-panel,
  .ds-admin-inspired .year-card,
  .ds-admin-inspired .module-card,
  .ds-admin-inspired .question-card,
  .ds-admin-inspired .empty-card,
  .ds-admin-inspired .item,
  .ds-admin-inspired .version-row,
  .ds-admin-inspired .empty-stats,
  .ds-admin-inspired .timer-box,
  .ds-admin-inspired .rank-card {
    border-radius: 8px !important;
    background-image: none !important;
    box-shadow: none !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
  }

  .ds-admin-inspired .card,
  .ds-admin-inspired .panel,
  .ds-admin-inspired .stat-card,
  .ds-admin-inspired .stat,
  .ds-admin-inspired .mission,
  .ds-admin-inspired .achievement-card,
  .ds-admin-inspired .library-panel,
  .ds-admin-inspired .year-card,
  .ds-admin-inspired .module-card {
    background-color: var(--surface, #111c2d) !important;
    border-color: var(--border, #1e2f47) !important;
  }

  .ds-admin-inspired .card:hover,
  .ds-admin-inspired .panel:hover,
  .ds-admin-inspired .stat-card:hover,
  .ds-admin-inspired .stat:hover,
  .ds-admin-inspired .mission:hover,
  .ds-admin-inspired .achievement-card:hover,
  .ds-admin-inspired .module-card:hover {
    transform: none !important;
    box-shadow: none !important;
    border-color: var(--border-hover, #2c4168) !important;
  }

  .ds-admin-inspired .toolbar {
    padding: 20px 24px;
    background: var(--surface, #111c2d) !important;
    background-image: none !important;
    border-color: var(--border, #1e2f47) !important;
  }

  .ds-admin-inspired .input,
  .ds-admin-inspired .select,
  .ds-admin-inspired .textarea,
  .ds-admin-inspired input:not([type="checkbox"]):not([type="radio"]),
  .ds-admin-inspired select,
  .ds-admin-inspired textarea {
    border-radius: 6px !important;
    box-shadow: none;
  }

  .ds-admin-inspired .input:focus,
  .ds-admin-inspired .select:focus,
  .ds-admin-inspired .textarea:focus,
  .ds-admin-inspired input:focus,
  .ds-admin-inspired select:focus,
  .ds-admin-inspired textarea:focus {
    border-color: var(--accent, #3b82f6) !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, .10) !important;
  }

  .ds-admin-inspired .btn,
  .ds-admin-inspired .header-action,
  .ds-admin-inspired .leaderboard-link,
  .ds-admin-inspired .open-link,
  .ds-admin-inspired .chip,
  .ds-admin-inspired .alert {
    border-radius: 6px !important;
    box-shadow: none !important;
    font-weight: 600 !important;
    transform: none !important;
  }

  .ds-admin-inspired .btn:hover,
  .ds-admin-inspired .header-action:hover,
  .ds-admin-inspired .leaderboard-link:hover,
  .ds-admin-inspired .open-link:hover {
    transform: none !important;
    box-shadow: none !important;
  }

  .ds-admin-inspired .leaderboard-link {
    min-height: 36px;
    padding: 8px 16px;
    border: 1px solid var(--border, #1e2f47);
    background: var(--surface2, #1a2638) !important;
    color: var(--text, #fafafa);
  }

  .ds-admin-inspired .section-title {
    margin: 28px 0 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border, #1e2f47);
    font-size: 1rem;
    font-weight: 600;
  }

  .ds-admin-inspired .bar,
  .ds-admin-inspired .fill,
  .ds-admin-inspired .progress,
  .ds-admin-inspired .progress > span {
    border-radius: 4px !important;
    box-shadow: none !important;
  }

  .ds-admin-inspired .day {
    border-radius: 3px 3px 0 0 !important;
    background: var(--accent, #3b82f6) !important;
  }

  .ds-admin-inspired .empty-icon,
  .ds-admin-inspired .stat-icon {
    border-radius: 6px !important;
    box-shadow: none !important;
  }

  @media (max-width: 700px) {
    .ds-admin-inspired .ds-main,
    .ds-admin-inspired .main,
    .ds-admin-inspired .content {
      padding: 18px !important;
    }
  }
</style>
@endonce
