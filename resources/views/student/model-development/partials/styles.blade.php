<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--surface3:#0f1928;--border:#1e2f47;--border-hover:#2c4168;--accent:#3b82f6;--text:#fafafa;--muted:#7f93b0;--dim:#3d5272;--radius:8px;--radius-sm:6px;--ml-bg:#0d1320;--ml-card:#111c2d;--ml-card2:#1a2638;--ml-border:#1e2f47;--ml-text:#fafafa;--ml-muted:#7f93b0;--ml-blue:#3b82f6;--ml-blue2:#2563eb;--ml-green:#10b981;--ml-yellow:#f59e0b;--ml-red:#ef4444;--ml-radius:8px}*{box-sizing:border-box}body{margin:0;background:var(--ml-bg);color:var(--ml-text);font-family:Inter,Arial,sans-serif}.ml-layout{display:flex;min-height:100vh}.ml-main{flex:1;min-width:0;padding:32px;overflow:auto}.ml-head{display:flex;justify-content:space-between;align-items:flex-start;gap:18px;margin-bottom:24px}.ml-title{margin:0 0 8px;font-size:1.375rem;font-weight:700;letter-spacing:-.025em}.ml-subtitle,.ml-muted{margin:0;color:var(--ml-muted);line-height:1.6}.ml-actions{display:flex;gap:10px;flex-wrap:wrap}.ml-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 14px;border:1px solid transparent;border-radius:6px;background:var(--ml-blue);color:white;text-decoration:none;font:600 .84rem Inter,Arial;cursor:pointer}.ml-btn:hover{background:var(--ml-blue2)}.ml-btn.secondary{background:var(--ml-card2);border-color:var(--ml-border);color:var(--ml-text)}.ml-btn.danger{background:transparent;border-color:rgba(239,68,68,.55);color:#fca5a5}.ml-btn.small{padding:7px 10px;font-size:.75rem}.ml-card{background:var(--ml-card);border:1px solid var(--ml-border);border-radius:var(--ml-radius);padding:20px}.ml-section{margin:26px 0}.ml-section-head{display:flex;justify-content:space-between;align-items:end;gap:16px;margin-bottom:12px}.ml-section-title{font-size:1rem;font-weight:700;margin:0 0 5px}.ml-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.ml-grid.two{grid-template-columns:repeat(2,minmax(0,1fr))}.ml-grid.four{grid-template-columns:repeat(4,minmax(0,1fr))}.ml-dataset{display:flex;flex-direction:column;min-height:265px}.ml-dataset h3{font-size:1rem;margin:0 0 8px}.ml-dataset .ml-actions{margin-top:auto}.ml-badge{display:inline-flex;border:1px solid var(--ml-border);border-radius:999px;padding:5px 9px;font-size:.7rem;font-weight:700;color:var(--ml-muted);background:var(--ml-card2)}.ml-badge.good{color:#6ee7b7;border-color:rgba(16,185,129,.4)}.ml-badge.warn{color:#fcd34d;border-color:rgba(245,158,11,.4)}.ml-badge.bad{color:#fca5a5;border-color:rgba(239,68,68,.4)}.ml-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px;margin:15px 0}.ml-meta div{background:var(--ml-card2);border:1px solid var(--ml-border);border-radius:6px;padding:10px}.ml-meta span{display:block;color:var(--ml-muted);font-size:.68rem;text-transform:uppercase;letter-spacing:.05em}.ml-meta strong{display:block;margin-top:4px;font-size:.86rem}.ml-table-wrap{overflow:auto;border:1px solid var(--ml-border);border-radius:8px}.ml-table{width:100%;border-collapse:collapse}.ml-table th,.ml-table td{padding:11px 12px;text-align:left;border-bottom:1px solid var(--ml-border);font-size:.8rem;white-space:nowrap}.ml-table th{font-size:.68rem;text-transform:uppercase;letter-spacing:.06em;color:var(--ml-muted);background:var(--ml-card2)}.ml-table tr:last-child td{border-bottom:0}.ml-field{margin-bottom:15px}.ml-label{display:block;font-size:.76rem;font-weight:700;margin-bottom:7px}.ml-input,.ml-select,.ml-file{width:100%;border:1px solid var(--ml-border);border-radius:6px;background:var(--ml-card2);color:var(--ml-text);padding:10px 11px;font:inherit;font-size:.84rem}.ml-help{font-size:.72rem;color:var(--ml-muted);line-height:1.45;margin-top:5px}.ml-upload{border:1px dashed #36577d;border-radius:8px;padding:18px;background:rgba(59,130,246,.04)}.ml-alert{padding:12px 14px;border:1px solid rgba(16,185,129,.45);background:rgba(16,185,129,.08);color:#a7f3d0;border-radius:8px;margin-bottom:18px;font-size:.84rem}.ml-alert.error{border-color:rgba(239,68,68,.5);background:rgba(239,68,68,.08);color:#fecaca}.ml-stat strong{display:block;font-size:1.35rem;margin-top:7px}.ml-stat span{font-size:.72rem;color:var(--ml-muted);text-transform:uppercase;letter-spacing:.06em}.ml-progress{height:12px;background:#08101d;border:1px solid var(--ml-border);border-radius:4px;overflow:hidden}.ml-progress>div{height:100%;background:var(--ml-blue);transition:width .4s}.ml-quality{font-size:2.25rem;font-weight:800}.ml-list{margin:10px 0 0;padding-left:18px;color:var(--ml-muted);line-height:1.65;font-size:.83rem}.ml-tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:15px}.ml-step{display:none}.ml-step.active{display:block}.ml-stepper{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:8px;margin-bottom:18px}.ml-step-indicator{padding:10px 8px;border:1px solid var(--ml-border);border-radius:6px;background:var(--ml-card);font-size:.72rem;color:var(--ml-muted);text-align:center}.ml-step-indicator.active{color:white;border-color:var(--ml-blue);background:rgba(59,130,246,.12)}.ml-check-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px}.ml-check{display:flex;gap:8px;align-items:flex-start;border:1px solid var(--ml-border);background:var(--ml-card2);border-radius:6px;padding:10px;font-size:.8rem}.ml-algorithm{cursor:pointer}.ml-algorithm input{margin-top:3px}.ml-chart{display:block;width:100%;max-height:520px;object-fit:contain;border:1px solid var(--ml-border);border-radius:8px;background:white}.ml-metric{font-size:1.2rem;font-weight:800}.ml-status-dot{width:8px;height:8px;border-radius:50%;display:inline-block;background:var(--ml-muted);margin-right:6px}.ml-status-dot.completed,.ml-status-dot.ready{background:var(--ml-green)}.ml-status-dot.running,.ml-status-dot.queued{background:var(--ml-yellow)}.ml-status-dot.failed{background:var(--ml-red)}@media(max-width:1150px){.ml-grid,.ml-grid.four{grid-template-columns:repeat(2,minmax(0,1fr))}.ml-stepper{grid-template-columns:repeat(3,minmax(0,1fr))}}@media(max-width:760px){.ml-layout{display:block}.ml-main{padding:20px}.ml-head{display:block}.ml-actions{margin-top:12px}.ml-grid,.ml-grid.two,.ml-grid.four,.ml-check-grid{grid-template-columns:1fr}.ml-stepper{grid-template-columns:repeat(2,minmax(0,1fr))}}

.ml-roadmap-number{display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;margin-right:5px;border:1px solid var(--ml-border);font-size:.68rem}.ml-step-indicator.complete{color:#a7f3d0;border-color:rgba(16,185,129,.4);background:rgba(16,185,129,.08)}.ml-step-indicator.complete .ml-roadmap-number{border-color:rgba(16,185,129,.55);color:#6ee7b7}.ml-recommended-setup,.ml-recommended-model{display:flex;gap:16px;border:1px solid rgba(245,158,11,.5);background:rgba(245,158,11,.07);border-radius:10px;padding:18px}.ml-recommended-icon{font-size:1.6rem;line-height:1}.ml-recommended-content{min-width:0;flex:1}.ml-recommended-kicker{display:block;color:#fcd34d;font-size:.7rem;font-weight:800;letter-spacing:.07em;text-transform:uppercase}.ml-recommended-content h3,.ml-recommended-model h3{margin:6px 0 8px;font-size:1.05rem}.ml-recommended-content p,.ml-recommended-model p{margin:10px 0 14px;color:#c8d5e8;font-size:.82rem;line-height:1.6}.ml-recommended-summary{display:flex;gap:8px;flex-wrap:wrap}.ml-recommended-summary span{background:rgba(13,19,32,.55);border:1px solid var(--ml-border);border-radius:8px;padding:7px 9px;font-size:.75rem}.ml-recommended-inline{border:1px solid rgba(245,158,11,.35);background:rgba(245,158,11,.05);border-radius:8px;padding:12px 14px;margin:8px 0}.ml-feature-pills{display:flex;gap:7px;flex-wrap:wrap;margin-top:9px}.ml-feature-pills span{border:1px solid var(--ml-border);background:var(--ml-card2);border-radius:999px;padding:5px 9px;font-size:.72rem;color:#d8e3f2}.ml-recommended-star{font-size:.74rem;margin-left:3px}.ml-recommended-model{display:block}.ml-recommended-model.is-mismatch{display:none}.ml-recommended-model-head{display:flex;align-items:flex-start;justify-content:space-between;gap:14px}.ml-recommended-radio-card{border-color:rgba(245,158,11,.4);background:rgba(245,158,11,.04)}.ml-advanced{border:1px solid var(--ml-border);border-radius:8px;background:var(--ml-card2);padding:13px 14px}.ml-advanced summary{cursor:pointer;font-size:.82rem;font-weight:700}.ml-review-card span{display:block;color:var(--ml-muted);font-size:.7rem;text-transform:uppercase;letter-spacing:.06em}.ml-review-card strong{display:block;margin-top:7px;font-size:1rem}.ml-review-card small{display:block;margin-top:6px;color:#fcd34d}.ml-problem-choice{min-height:150px}

/* Shared ten-stage learning roadmap. */
.ml-roadmap-layout{display:grid;grid-template-columns:minmax(250px,290px) minmax(0,1fr);gap:22px;align-items:start}.ml-roadmap-column{position:sticky;top:20px;min-width:0}.ml-roadmap-content{min-width:0}.ml-roadmap-panel{background:var(--ml-card);border:1px solid var(--ml-border);border-radius:8px;padding:16px}.ml-roadmap-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}.ml-roadmap-heading strong{display:block;margin-top:5px;font-size:.82rem}.ml-roadmap-eyebrow{display:block;color:var(--ml-muted);font-size:.66rem;font-weight:800;text-transform:uppercase;letter-spacing:.07em}.ml-roadmap-percent{font-size:1.05rem;font-weight:800;color:#dbeafe}.ml-roadmap-progress{height:9px;margin:13px 0 8px}.ml-roadmap-position{margin:0 0 14px;color:#bfdbfe;font-size:.74rem;font-weight:700}.ml-roadmap-list{list-style:none;margin:0;padding:0}.ml-roadmap-item{position:relative;padding-bottom:7px}.ml-roadmap-item:last-child{padding-bottom:0}.ml-roadmap-item::after{content:"";position:absolute;left:17px;top:39px;bottom:-1px;width:2px;background:var(--ml-border)}.ml-roadmap-item:last-child::after{display:none}.ml-roadmap-action{position:relative;z-index:1;width:100%;display:grid;grid-template-columns:34px minmax(0,1fr);gap:10px;align-items:start;margin:0;padding:10px;border:1px solid transparent;border-radius:7px;background:transparent;color:inherit;text-align:left;text-decoration:none;font:inherit}.ml-roadmap-item button.ml-roadmap-action{cursor:pointer}.ml-roadmap-item button.ml-roadmap-action:disabled{cursor:not-allowed}.ml-roadmap-action[aria-disabled="true"]{pointer-events:none}.ml-roadmap-step-number{width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--ml-border);border-radius:50%;background:#0d1727;color:var(--ml-muted);font-size:.76rem;font-weight:800}.ml-roadmap-copy{min-width:0}.ml-roadmap-copy strong{display:block;font-size:.79rem}.ml-roadmap-copy small{display:block;margin-top:3px;color:var(--ml-muted);font-size:.67rem;line-height:1.35}.ml-roadmap-state{display:block;margin-top:5px;color:var(--ml-muted);font-size:.61rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase}.ml-roadmap-item.current .ml-roadmap-action,.ml-roadmap-item.current-complete .ml-roadmap-action{border-color:rgba(59,130,246,.62);background:rgba(59,130,246,.11)}.ml-roadmap-item.current .ml-roadmap-step-number,.ml-roadmap-item.current-complete .ml-roadmap-step-number{border-color:var(--ml-blue);background:var(--ml-blue);color:#fff}.ml-roadmap-item.current .ml-roadmap-state{color:#93c5fd}.ml-roadmap-item.complete .ml-roadmap-action{background:rgba(16,185,129,.055)}.ml-roadmap-item.complete .ml-roadmap-step-number,.ml-roadmap-item.current-complete .ml-roadmap-step-number{border-color:rgba(16,185,129,.7);background:rgba(16,185,129,.14);color:#6ee7b7}.ml-roadmap-item.complete .ml-roadmap-state,.ml-roadmap-item.current-complete .ml-roadmap-state{color:#6ee7b7}.ml-roadmap-item.error .ml-roadmap-action{border-color:rgba(239,68,68,.58);background:rgba(239,68,68,.09)}.ml-roadmap-item.error .ml-roadmap-step-number{border-color:var(--ml-red);color:#fca5a5}.ml-roadmap-item.error .ml-roadmap-state{color:#fca5a5}.ml-roadmap-item.locked{opacity:.58}.ml-roadmap-item.locked .ml-roadmap-action{background:rgba(15,25,40,.35)}

.ml-workflow-step{display:none}.ml-workflow-step.active{display:block}.ml-step-kicker{display:block;color:#93c5fd;font-size:.69rem;font-weight:800;text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px}.ml-step-error{display:none;margin-top:14px}.ml-step-error.visible{display:block}.ml-step-navigation{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:16px}.ml-step-navigation .ml-actions{margin:0}.ml-selection-summary{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin:16px 0}.ml-summary-row{border:1px solid var(--ml-border);background:var(--ml-card2);border-radius:7px;padding:11px}.ml-summary-row span{display:block;color:var(--ml-muted);font-size:.67rem;text-transform:uppercase;letter-spacing:.06em}.ml-summary-row strong{display:block;margin-top:5px;font-size:.86rem;overflow-wrap:anywhere}.ml-summary-row.full{grid-column:1/-1}.ml-summary-list{display:flex;flex-wrap:wrap;gap:6px;margin-top:7px}.ml-summary-list span{color:#dbeafe;text-transform:none;letter-spacing:0;border:1px solid var(--ml-border);border-radius:999px;padding:4px 7px}.ml-training-milestone{border-color:rgba(59,130,246,.58);box-shadow:0 0 0 1px rgba(59,130,246,.08)}.ml-training-ready{display:flex;align-items:center;gap:12px;padding:13px 14px;border:1px solid rgba(16,185,129,.45);background:rgba(16,185,129,.07);border-radius:7px;color:#a7f3d0;margin:16px 0}.ml-training-ready-icon{width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center;border:1px solid rgba(16,185,129,.5);border-radius:50%;font-weight:800}.ml-training-stage-list{display:grid;gap:8px;margin:18px 0}.ml-training-stage{display:grid;grid-template-columns:24px minmax(0,1fr);gap:9px;align-items:center;padding:9px 10px;border:1px solid var(--ml-border);border-radius:7px;background:var(--ml-card2);color:var(--ml-muted);font-size:.8rem}.ml-training-stage-marker{width:18px;height:18px;display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--ml-border);border-radius:50%;font-size:.62rem}.ml-training-stage.complete{color:#a7f3d0;border-color:rgba(16,185,129,.35)}.ml-training-stage.complete .ml-training-stage-marker{border-color:var(--ml-green);background:rgba(16,185,129,.14)}.ml-training-stage.current{color:#dbeafe;border-color:rgba(59,130,246,.48);background:rgba(59,130,246,.08)}.ml-training-stage.current .ml-training-stage-marker{border-color:var(--ml-blue);background:var(--ml-blue);color:#fff}.ml-training-stage.error{color:#fecaca;border-color:rgba(239,68,68,.5)}.ml-success-milestone{display:none;border:1px solid rgba(16,185,129,.52);background:rgba(16,185,129,.07);border-radius:8px;padding:18px;margin-top:18px}.ml-success-milestone.visible{display:block}.ml-success-title{margin:0;color:#a7f3d0;font-size:1.05rem}.ml-success-metrics{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:9px;margin:14px 0}.ml-success-metric{border:1px solid var(--ml-border);background:var(--ml-card);border-radius:7px;padding:10px}.ml-success-metric span{display:block;color:var(--ml-muted);font-size:.65rem;text-transform:uppercase;letter-spacing:.05em}.ml-success-metric strong{display:block;margin-top:5px;font-size:.93rem}.ml-result-section{margin-bottom:20px}.ml-result-section[hidden]{display:none!important}.ml-saved-milestone{border:1px solid rgba(16,185,129,.48);background:rgba(16,185,129,.07);border-radius:8px;padding:20px}.ml-saved-milestone h2{margin:0 0 7px;color:#a7f3d0}.ml-inline-note{padding:10px 12px;border-left:3px solid var(--ml-blue);background:rgba(59,130,246,.06);color:var(--ml-muted);font-size:.78rem;line-height:1.5;margin:12px 0}.ml-dataset-choice{display:flex;flex-direction:column;min-height:230px}.ml-dataset-choice .ml-actions{margin-top:auto}.ml-history-section{margin-top:30px;padding-top:24px;border-top:1px solid var(--ml-border)}

@media(max-width:1180px){.ml-roadmap-layout{grid-template-columns:minmax(225px,255px) minmax(0,1fr)}.ml-success-metrics{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:900px){.ml-roadmap-layout{grid-template-columns:1fr}.ml-roadmap-column{position:static}.ml-roadmap-panel{padding:14px}.ml-roadmap-list{display:grid;grid-template-columns:1fr;gap:7px}.ml-roadmap-item{padding:0}.ml-roadmap-item::after{display:none}.ml-roadmap-action{height:100%}}
@media(max-width:620px){.ml-roadmap-list{grid-template-columns:1fr}.ml-selection-summary,.ml-success-metrics{grid-template-columns:1fr}.ml-summary-row.full{grid-column:auto}.ml-step-navigation{align-items:stretch;flex-direction:column}.ml-step-navigation .ml-btn,.ml-step-navigation .ml-actions,.ml-step-navigation .ml-actions .ml-btn{width:100%}.ml-roadmap-copy small{font-size:.7rem}}
.ml-status-dot.retrying{background:var(--ml-yellow)}

.ml-main [hidden]{display:none!important}
.ml-badge{white-space:nowrap;align-items:center}
/* Beginner guidance layer: tactile surfaces, step headers, guides, and result explanations. */
.ml-card{background:linear-gradient(180deg,rgba(255,255,255,.028),rgba(255,255,255,0) 55%),var(--ml-card);box-shadow:0 1px 0 rgba(255,255,255,.04) inset,0 12px 26px -18px rgba(0,0,0,.85),0 2px 4px rgba(0,0,0,.22)}
.ml-btn{box-shadow:0 1px 0 rgba(255,255,255,.2) inset,0 3px 0 #1d4ed8,0 8px 16px -8px rgba(37,99,235,.65);transition:transform .08s ease,box-shadow .08s ease,background .15s ease}
.ml-btn:active{transform:translateY(2px);box-shadow:0 1px 0 rgba(255,255,255,.2) inset,0 1px 0 #1d4ed8}
.ml-btn.secondary{box-shadow:0 1px 0 rgba(255,255,255,.05) inset,0 3px 0 #0a111c,0 8px 14px -10px rgba(0,0,0,.8)}
.ml-btn.secondary:hover{background:#213049;border-color:var(--border-hover)}
.ml-btn.secondary:active{box-shadow:0 1px 0 rgba(255,255,255,.05) inset,0 1px 0 #0a111c}
.ml-btn.danger{box-shadow:none}.ml-btn.danger:hover{background:rgba(239,68,68,.12)}
.ml-btn:disabled{opacity:.55;cursor:not-allowed;transform:none}
.ml-input:focus,.ml-select:focus,.ml-file:focus{outline:none;border-color:var(--ml-blue);box-shadow:0 0 0 3px rgba(59,130,246,.18)}
.ml-subtitle-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:2px}

.ml-step-head{display:flex;align-items:flex-start;gap:14px}
.ml-step-disc{flex:none;width:42px;height:42px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:1rem;color:#fff;background:linear-gradient(180deg,#5b97f8,#2563eb);box-shadow:0 1px 0 rgba(255,255,255,.28) inset,0 3px 0 #1e40af,0 10px 18px -10px rgba(37,99,235,.9)}
.ml-step-head-copy{flex:1;min-width:0}.ml-step-head-copy h2{font-size:1.12rem;margin:2px 0 4px}
.ml-step-count{flex:none;font-size:.72rem;color:var(--ml-muted);border:1px solid var(--ml-border);border-radius:999px;padding:4px 10px;background:var(--ml-card2)}

.ml-guide{margin-top:16px;border:1px solid var(--ml-border);border-radius:10px;background:var(--surface3)}
.ml-guide>summary{list-style:none;cursor:pointer;padding:11px 14px;font-size:.8rem;font-weight:700;display:flex;align-items:center;gap:9px;color:#dbeafe}
.ml-guide>summary::-webkit-details-marker{display:none}
.ml-guide>summary::before{content:"?";flex:none;width:20px;height:20px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:rgba(59,130,246,.18);color:#93c5fd;font-size:.7rem}
.ml-guide>summary::after{content:"Show";margin-left:auto;color:var(--ml-muted);font-weight:600;font-size:.72rem}
.ml-guide[open]>summary::after{content:"Hide"}
.ml-guide-body{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;padding:0 14px 14px}
.ml-guide-item{border:1px solid var(--ml-border);background:var(--ml-card2);border-radius:8px;padding:10px 12px}
.ml-guide-item h4{margin:0 0 4px;font-size:.74rem;color:#bfdbfe}
.ml-guide-item p{margin:0;color:#c8d5e8;font-size:.79rem;line-height:1.55}
.ml-guide-item.wide{grid-column:1/-1}
.ml-guide-item.tip{border-color:rgba(16,185,129,.35)}.ml-guide-item.tip h4{color:#6ee7b7}
.ml-guide-item.warn{border-color:rgba(245,158,11,.35)}.ml-guide-item.warn h4{color:#fcd34d}

.ml-insight{margin-top:14px;border:1px solid var(--ml-border);border-radius:10px;background:var(--ml-card2);padding:14px;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}
.ml-insight[hidden]{display:none}
.ml-insight-cell span{display:block;font-size:.66rem;color:var(--ml-muted);text-transform:uppercase;letter-spacing:.05em}
.ml-insight-cell strong{display:block;margin-top:4px;font-size:.88rem;overflow-wrap:anywhere}
.ml-insight-note{grid-column:1/-1;font-size:.8rem;color:#c8d5e8;line-height:1.55;border-top:1px solid var(--ml-border);padding-top:10px}
.ml-insight-note.warn{color:#fcd34d}

.ml-toolbar{display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-top:16px}
.ml-counter{margin-left:auto;font-size:.76rem;color:var(--ml-muted)}.ml-counter strong{color:var(--ml-text)}
.ml-check{cursor:pointer;transition:border-color .15s ease,background .15s ease}
.ml-check:has(input:checked){border-color:rgba(59,130,246,.62);background:rgba(59,130,246,.09)}
.ml-check:has(input:disabled){opacity:.55;cursor:not-allowed}
.ml-check.is-target{border-style:dashed}
.ml-type-chip{display:inline-block;font-size:.63rem;font-weight:700;border-radius:999px;padding:2px 7px;border:1px solid var(--ml-border);color:var(--ml-muted);margin:4px 4px 0 0}
.ml-type-chip.num{color:#93c5fd;border-color:rgba(59,130,246,.42)}
.ml-type-chip.cat{color:#fcd34d;border-color:rgba(245,158,11,.42)}
.ml-check-reason{display:block;font-size:.68rem;color:#fca5a5;margin-top:4px}
.ml-check-reason:empty{display:none}

.ml-algorithm{transition:border-color .15s ease,transform .15s ease,box-shadow .15s ease}
.ml-algorithm:hover{border-color:var(--border-hover);transform:translateY(-2px)}
.ml-algorithm:has(input:checked){border-color:var(--ml-blue);box-shadow:0 0 0 1px var(--ml-blue) inset,0 14px 28px -18px rgba(59,130,246,.9)}
.ml-choice-top{display:flex;align-items:center;justify-content:space-between;gap:8px}
.ml-badge-row{display:flex;gap:6px;flex-wrap:wrap;margin-top:10px}
.ml-badge.blue{color:#93c5fd;border-color:rgba(59,130,246,.45)}
.ml-badge[hidden]{display:none}
.ml-algo-analogy{margin:8px 0 0;font-size:.78rem;color:#c8d5e8;line-height:1.5}
.ml-algo-more{margin-top:10px}.ml-algo-more summary{cursor:pointer;font-size:.74rem;font-weight:700;color:#93c5fd}
.ml-mini-list{margin:6px 0 0;padding-left:16px;font-size:.74rem;color:var(--ml-muted);line-height:1.5}
.ml-mini-title{display:block;margin-top:8px;font-size:.7rem;font-weight:700;color:#dbeafe}

.ml-split-visual{margin-top:14px}
.ml-split-bar{display:flex;height:36px;border-radius:9px;overflow:hidden;border:1px solid var(--ml-border);box-shadow:0 3px 0 #08101d}
.ml-split-train{background:linear-gradient(180deg,#5b97f8,#2563eb);display:flex;align-items:center;padding:0 12px;font-size:.74rem;font-weight:700;color:#fff;transition:width .3s ease;white-space:nowrap;overflow:hidden}
.ml-split-test{flex:1;min-width:0;background:linear-gradient(180deg,#34d399,#059669);display:flex;align-items:center;justify-content:flex-end;padding:0 12px;font-size:.74rem;font-weight:700;color:#04241a;white-space:nowrap;overflow:hidden}
.ml-split-legend{display:flex;justify-content:space-between;gap:10px;margin-top:9px;font-size:.78rem;color:var(--ml-muted)}
.ml-split-legend strong{color:var(--ml-text)}

.ml-review-list{display:grid;gap:8px;margin:16px 0}
.ml-review-item{display:grid;grid-template-columns:28px minmax(0,1fr) auto;gap:12px;align-items:center;border:1px solid var(--ml-border);background:var(--ml-card2);border-radius:8px;padding:10px 12px}
.ml-review-mark{width:26px;height:26px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:800;border:1px solid rgba(16,185,129,.55);color:#6ee7b7;background:rgba(16,185,129,.12)}
.ml-review-item.bad{border-color:rgba(239,68,68,.45)}
.ml-review-item.bad .ml-review-mark{border-color:rgba(239,68,68,.55);color:#fca5a5;background:rgba(239,68,68,.1)}
.ml-review-label{display:block;font-size:.68rem;color:var(--ml-muted);text-transform:uppercase;letter-spacing:.05em}
.ml-review-value{display:block;margin-top:3px;font-size:.85rem;font-weight:600;overflow-wrap:anywhere}
.ml-link-btn{background:none;border:0;color:#93c5fd;font:600 .74rem Inter,Arial;cursor:pointer;padding:5px 8px;border-radius:5px}
.ml-link-btn:hover{background:rgba(59,130,246,.12)}

.ml-verdict{display:grid;grid-template-columns:auto minmax(0,1fr);gap:20px;align-items:center;border-radius:12px;padding:20px;border:1px solid var(--ml-border);background:linear-gradient(180deg,rgba(255,255,255,.03),rgba(255,255,255,0) 60%),var(--ml-card);box-shadow:0 14px 30px -20px rgba(0,0,0,.9)}
.ml-verdict-score{width:118px;height:118px;border-radius:50%;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;background:radial-gradient(circle at 35% 30%,#24344f,#0f1928 70%);box-shadow:0 1px 0 rgba(255,255,255,.08) inset,0 12px 22px -10px rgba(0,0,0,.85);border:3px solid var(--ml-muted)}
.ml-verdict-score strong{font-size:1.15rem}
.ml-verdict-score span{font-size:.6rem;color:var(--ml-muted);text-transform:uppercase;letter-spacing:.05em;margin-top:3px}
.ml-verdict h3{margin:0 0 6px;font-size:1.08rem}
.ml-verdict p{margin:0;color:#c8d5e8;font-size:.85rem;line-height:1.55}
.ml-verdict ul{margin:10px 0 0;padding-left:18px;color:var(--ml-muted);font-size:.8rem;line-height:1.6}
.ml-verdict-foot{font-size:.7rem;color:var(--ml-muted);margin-top:10px}
.ml-verdict.strong{border-color:rgba(16,185,129,.5)}.ml-verdict.strong .ml-verdict-score{border-color:var(--ml-green)}.ml-verdict.strong h3{color:#a7f3d0}
.ml-verdict.fair{border-color:rgba(245,158,11,.5)}.ml-verdict.fair .ml-verdict-score{border-color:var(--ml-yellow)}.ml-verdict.fair h3{color:#fcd34d}
.ml-verdict.weak{border-color:rgba(239,68,68,.5)}.ml-verdict.weak .ml-verdict-score{border-color:var(--ml-red)}.ml-verdict.weak h3{color:#fecaca}

.ml-stat small{display:block;margin-top:6px;color:var(--ml-muted);font-size:.72rem;line-height:1.45;text-transform:none;letter-spacing:0}
.ml-better{display:inline-block;margin-top:8px;font-size:.62rem;font-weight:700;color:#93c5fd;text-transform:uppercase;letter-spacing:.05em}
.ml-cm-wrap{overflow:auto}
.ml-cm{border-collapse:separate;border-spacing:4px;margin-top:12px}
.ml-cm th{font-size:.68rem;color:var(--ml-muted);font-weight:700;padding:4px 6px;text-align:center;white-space:nowrap}
.ml-cm th.row{text-align:right}
.ml-cm td{min-width:52px;height:40px;text-align:center;border-radius:6px;font-weight:700;font-size:.82rem;background:var(--ml-card2);border:1px solid var(--ml-border)}
.ml-cm td.hit{background:rgba(16,185,129,.16);border-color:rgba(16,185,129,.45);color:#a7f3d0}
.ml-cm td.miss{background:rgba(239,68,68,.12);border-color:rgba(239,68,68,.4);color:#fecaca}
.ml-chart-caption{margin:10px 0 0;font-size:.76rem;color:var(--ml-muted);line-height:1.5}
.ml-range{display:flex;justify-content:space-between;gap:8px;font-size:.68rem;color:var(--ml-muted);margin-top:5px}
.ml-range-warn{display:none;font-size:.7rem;color:#fcd34d;margin-top:4px}
.ml-range-warn.visible{display:block}

.ml-phases{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:16px}
.ml-phase{border:1px solid var(--ml-border);border-radius:10px;background:var(--ml-card2);padding:14px;box-shadow:0 3px 0 #0a111c}
.ml-phase h3{display:flex;align-items:center;gap:8px;margin:0 0 6px;font-size:.9rem}
.ml-phase b{display:inline-flex;width:26px;height:26px;border-radius:8px;align-items:center;justify-content:center;background:rgba(59,130,246,.16);color:#93c5fd;font-size:.78rem}
.ml-phase small{color:var(--ml-muted);font-size:.7rem;margin-left:auto;font-weight:600}
.ml-phase p{margin:0;color:var(--ml-muted);font-size:.78rem;line-height:1.5}
.ml-starter{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:16px;align-items:center;margin-top:16px;border-color:rgba(59,130,246,.55);background:linear-gradient(135deg,rgba(59,130,246,.13),rgba(59,130,246,.02) 60%),var(--ml-card)}
.ml-starter h3{margin:0 0 5px;font-size:1rem}
.ml-resume{border-color:rgba(245,158,11,.5)}
.ml-dataset-choice .ml-recommended-line{font-size:.74rem;color:#c8d5e8;margin:0 0 12px}

.ml-wait-note{display:none;margin-top:12px}.ml-wait-note.visible{display:block}
.ml-stage-copy{display:block;font-size:.72rem;color:var(--ml-muted);margin-top:2px}
.ml-training-stage.current .ml-stage-copy{color:#bfdbfe}
.ml-fix-list{margin:8px 0 0;padding-left:18px;font-size:.8rem;line-height:1.6}

.ml-recap{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:6px 16px;margin:14px 0 0;padding:0;list-style:none}
.ml-recap li{font-size:.8rem;color:#c8d5e8}.ml-recap li::before{content:"✓";color:#6ee7b7;font-weight:800;margin-right:8px}
.ml-readiness{list-style:none;margin:12px 0 0;padding:0;display:grid;gap:8px}
.ml-readiness li{display:flex;gap:9px;align-items:flex-start;font-size:.8rem;color:#c8d5e8;line-height:1.45}
.ml-readiness i{font-style:normal;flex:none;width:20px;height:20px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:800}
.ml-readiness i.ok{background:rgba(16,185,129,.14);color:#6ee7b7;border:1px solid rgba(16,185,129,.45)}
.ml-readiness i.warn{background:rgba(245,158,11,.12);color:#fcd34d;border:1px solid rgba(245,158,11,.45)}

.ml-roadmap-heading .ml-roadmap-title{margin-top:0;font-size:.9rem}.ml-roadmap-sub{display:block;margin-top:3px;color:var(--ml-muted);font-size:.72rem}.ml-roadmap-panel{box-shadow:0 12px 26px -18px rgba(0,0,0,.85)}
@media(max-width:760px){.ml-guide-body,.ml-insight,.ml-phases,.ml-recap{grid-template-columns:1fr}.ml-guide-item.wide,.ml-insight-note{grid-column:auto}.ml-verdict,.ml-starter{grid-template-columns:1fr}.ml-step-count{display:none}.ml-counter{margin-left:0;width:100%}}
@media(prefers-reduced-motion:reduce){.ml-btn,.ml-algorithm,.ml-split-train,.ml-progress>div{transition:none}.ml-algorithm:hover{transform:none}}
</style>
