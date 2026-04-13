<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module18LessonsSeeder
 * Seeds lessons for Module 18: Introduction to Privacy, Ethics & Data Governance.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module18LessonsSeeder
 */
class Module18LessonsSeeder extends Seeder
{
    public function run()
    {
        $module = Module::where('order_index', 18)->firstOrFail();
        Lesson::where('module_id', $module->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 18.1 — Why Ethics & Privacy Matter in Data Science
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>Why Ethics & Privacy Matter in Data Science</h2>
<p>Data science is not a neutral technical discipline. Every model you train, every dataset you collect, and every insight you publish has real consequences for real human beings. A loan model that unfairly rejects applicants. A facial recognition system that misidentifies people of color at higher rates. A health app that sells patient data to insurers. These are not hypothetical edge cases — they are documented failures that have already harmed millions of people. As a data scientist, you are one of the people with the power to cause or prevent them.</p>

<p>This module treats ethics, privacy, and governance not as compliance checklists to be tolerated, but as <strong>core professional competencies</strong> — as fundamental to your work as statistics or Python. The decisions you make about data collection, model design, and deployment have moral weight. This lesson establishes why that is true and what it demands of you.</p>

<h3>The Scale Problem: Why Data Science Ethics Is Different</h3>
<p>When a single doctor makes a biased diagnosis, one patient is harmed. When a biased algorithm makes the same mistake, it is applied identically to millions of people simultaneously — and often invisibly. This is the <strong>scale problem</strong>: automated data-driven systems amplify both the good and the harm of their underlying assumptions to a degree no individual human decision-maker ever could. A credit-scoring model is not one decision — it is one billion decisions per year, each shaped by the same hidden biases baked into training data collected decades ago.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CASE STUDY — Amazon's Hiring Algorithm (2018)</span>
    <span style="background:#f59e0b;color:#000;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;font-weight:600;">Real World</span>
  </div>
  <div style="padding:20px;">
    <p style="color:#e5e7eb;margin:0 0 12px;line-height:1.8;">Amazon built a machine learning system to screen job applicants automatically. After training on 10 years of historical hiring data, the system began systematically downgrading résumés that included the word <strong>"women's"</strong> (as in "women's chess club") and penalising graduates of all-women's colleges. The model had learned that men were historically hired more often — and replicated that pattern as a rule.</p>
    <p style="color:#e5e7eb;margin:0 0 12px;line-height:1.8;"><strong style="color:#f59e0b;">Root cause:</strong> The training data reflected historical human bias. The model learned to reproduce discrimination at scale — not because it was programmed to, but because that is what the data contained.</p>
    <p style="color:#e5e7eb;margin:0;line-height:1.8;"><strong style="color:#f59e0b;">Outcome:</strong> Amazon scrapped the tool entirely in 2018 after internal audits revealed the bias. The system had been operational long enough to affect an unknown number of real applicants.</p>
  </div>
</div>

<h3>The Five Core Ethical Principles for Data Scientists</h3>
<p>Multiple frameworks — from the EU's AI Act to Google's AI Principles — converge on the same foundational values. These five principles form the ethical bedrock of responsible data science practice:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — The Five Principles</span>
  </div>
  <div style="padding:20px;display:flex;flex-direction:column;gap:16px;">
    <div style="border-left:3px solid #10b981;padding-left:16px;">
      <strong style="color:#10b981;">1. Beneficence</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Your work should actively do good — for users, for society, for the communities whose data you are using. Ask not just "does this work?" but "does this help?"</p>
    </div>
    <div style="border-left:3px solid #3b82f6;padding-left:16px;">
      <strong style="color:#3b82f6;">2. Non-Maleficence</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">First, do no harm. Anticipate ways your model or system could be misused, could produce discriminatory outcomes, or could cause harm — and design to prevent those outcomes.</p>
    </div>
    <div style="border-left:3px solid #a78bfa;padding-left:16px;">
      <strong style="color:#a78bfa;">3. Autonomy</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Respect individuals' right to make informed decisions about their own data. This means meaningful consent, clear communication, and the ability to opt out.</p>
    </div>
    <div style="border-left:3px solid #f59e0b;padding-left:16px;">
      <strong style="color:#f59e0b;">4. Justice & Fairness</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Benefits and burdens should be distributed fairly. Systems must not systematically disadvantage people based on protected characteristics like race, gender, age, or disability.</p>
    </div>
    <div style="border-left:3px solid #ef4444;padding-left:16px;">
      <strong style="color:#ef4444;">5. Explicability</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Systems should be transparent and explainable. People affected by automated decisions have a right to understand why those decisions were made and how to challenge them.</p>
    </div>
  </div>
</div>

<h3>The Data Scientist's Responsibility: More Than Just Technical</h3>
<p>The classical view of data science treats the engineer as a neutral tool-builder: you build what you are asked, you optimize what you are measured on, and you leave the "ethics stuff" to lawyers and policy teams. This view is no longer acceptable — if it ever was. When you design a model, you are making choices about whose interests are optimized, whose data is included, what fairness even means, and who bears the risk if the system fails. These are inherently moral choices, not merely technical ones.</p>

<p>The good news: ethical data science does not require you to become a philosopher. It requires you to ask better questions at every stage of the data pipeline — during problem framing, data collection, feature engineering, model evaluation, and deployment. The rest of this module gives you the vocabulary, frameworks, and practical tools to ask those questions well.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '18.1 Why Ethics & Privacy Matter in Data Science',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L18_1', [
                ['q' => 'What is the "scale problem" in data science ethics?', 'opts' => ['Datasets are too large to process ethically', 'Automated systems apply the same flawed assumptions to millions of people simultaneously, amplifying harm far beyond what any individual decision-maker could', 'Data scientists work at companies that are too large to regulate', 'Privacy laws do not scale to large datasets'], 'ans' => 1, 'exp' => 'The scale problem refers to the fact that a biased or harmful algorithm is not one decision — it is the same decision applied identically to millions of people at once, making its impact orders of magnitude greater than individual human bias.'],
                ['q' => 'What was the root cause of bias in Amazon\'s 2018 hiring algorithm?', 'opts' => ['A programming error in the sorting function', 'The model was deliberately programmed to favour men', 'The training data reflected historical human hiring bias, which the model learned to replicate as a rule', 'The résumé database was corrupted'], 'ans' => 2, 'exp' => 'Amazon\'s model trained on 10 years of historical hiring decisions, in which men were hired more often. The model learned that pattern and reproduced it — not through deliberate programming, but by faithfully replicating biased historical data.'],
                ['q' => 'Which ethical principle states that individuals should have meaningful control over decisions about their own data?', 'opts' => ['Beneficence', 'Non-Maleficence', 'Autonomy', 'Explicability'], 'ans' => 2, 'exp' => 'Autonomy means respecting individuals\' right to make informed decisions about their own data — including the right to meaningful consent and the ability to opt out. It is the philosophical foundation of modern data privacy law.'],
                ['q' => 'Which ethical principle most directly demands that a data scientist consider ways their model could be misused before deploying it?', 'opts' => ['Justice & Fairness', 'Non-Maleficence', 'Beneficence', 'Explicability'], 'ans' => 1, 'exp' => 'Non-Maleficence — "first, do no harm" — requires proactively anticipating potential harms, misuses, and discriminatory outcomes before deployment. It is a preventive principle, not a reactive one.'],
                ['q' => 'Why is the view of the data scientist as a "neutral tool-builder" no longer acceptable?', 'opts' => ['Because data scientists must also be lawyers', 'Because every design decision — whose data is used, what is optimised, what fairness means — is an inherently moral choice, not a merely technical one', 'Because regulators now audit all model code', 'Because neutral tools do not generate profit'], 'ans' => 1, 'exp' => 'Every decision in a data pipeline carries moral weight: what to optimise, whose data to include, how to handle edge cases, who bears the risk of failure. Treating these as purely technical questions does not make them ethically neutral — it just makes the ethical choices invisible and unexamined.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 18.2 — Personal Data: Definitions, Types & Sensitivity
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Personal Data: Definitions, Types & Sensitivity</h2>
<p>Before you can protect personal data, you need to know what it is. This sounds obvious, but the boundaries of "personal data" are far wider and more nuanced than most people assume. A name and email address are clearly personal. But what about a device fingerprint? A browsing history? A combination of zip code, birth date, and gender that, taken together, can re-identify 87% of the US population? Understanding exactly what qualifies as personal data — and how sensitive different types are — is the foundation of every data privacy decision you will make.</p>

<h3>The Legal Definition: Personal Data vs. Personal Information</h3>
<p>Different privacy frameworks use slightly different terminology, but the core concept is consistent. Under the EU's <strong>General Data Protection Regulation (GDPR)</strong> — the world's most influential privacy law — <em>personal data</em> is defined as:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">DEFINITION — GDPR Article 4(1)</span>
  </div>
  <div style="padding:20px;">
    <p style="color:#a7f3d0;font-style:italic;font-size:1rem;line-height:1.8;margin:0 0 16px;">"Any information relating to an identified or <strong>identifiable</strong> natural person ('data subject'); an identifiable natural person is one who can be identified, directly or indirectly, in particular by reference to an identifier such as a name, an identification number, location data, an online identifier or to one or more factors specific to the physical, physiological, genetic, mental, economic, cultural or social identity of that natural person."</p>
    <p style="color:#9ca3af;margin:0;line-height:1.7;">The critical word is <strong style="color:#e5e7eb;">identifiable</strong>. Data does not need to contain your name to be personal — if it can be linked back to you, directly or through combination with other data, it is personal data and subject to protection.</p>
  </div>
</div>

<h3>A Taxonomy of Personal Data</h3>
<p>Personal data exists on a spectrum of directness and sensitivity. Understanding where different data types fall on that spectrum is essential for risk assessment and data handling decisions.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">TAXONOMY — Types of Personal Data</span>
  </div>
  <div style="padding:20px;display:flex;flex-direction:column;gap:20px;">
    <div>
      <p style="color:#10b981;font-weight:700;margin:0 0 8px;text-transform:uppercase;font-size:0.8rem;letter-spacing:0.05em;">Directly Identifying Data</p>
      <p style="color:#e5e7eb;margin:0 0 8px;line-height:1.7;">Uniquely and unambiguously identifies a specific individual without any additional information. Examples: full name, national ID number, passport number, biometric data, email address, phone number.</p>
    </div>
    <div style="border-top:1px solid var(--border);padding-top:20px;">
      <p style="color:#f59e0b;font-weight:700;margin:0 0 8px;text-transform:uppercase;font-size:0.8rem;letter-spacing:0.05em;">Indirectly Identifying Data (Quasi-Identifiers)</p>
      <p style="color:#e5e7eb;margin:0 0 8px;line-height:1.7;">Does not identify alone, but can be combined with other data to identify. Classic example: zip code (63,000 US zip codes) + date of birth + gender can uniquely identify 87% of Americans. Also includes: IP address, device ID, browsing history, location data, purchase history.</p>
    </div>
    <div style="border-top:1px solid var(--border);padding-top:20px;">
      <p style="color:#ef4444;font-weight:700;margin:0 0 8px;text-transform:uppercase;font-size:0.8rem;letter-spacing:0.05em;">Special Category (Sensitive) Data — Highest Protection</p>
      <p style="color:#e5e7eb;margin:0;line-height:1.7;">Data whose exposure creates elevated risk of discrimination, stigma, or serious harm. Under GDPR Article 9, this category requires explicit consent and stricter legal basis. Includes: racial or ethnic origin, political opinions, religious beliefs, trade union membership, genetic data, biometric data (for identification purposes), health data, sex life and sexual orientation, and criminal record data.</p>
    </div>
  </div>
</div>

<h3>The Re-Identification Problem</h3>
<p>One of the most dangerous misconceptions in data science is that <strong>removing names and ID numbers makes data anonymous</strong>. In most cases, it does not — it makes data <em>pseudonymous</em>, which is fundamentally different. Research by Latanya Sweeney at Harvard demonstrated that 87% of Americans can be uniquely identified using only three fields: zip code, date of birth, and gender. This combination is present in enormous numbers of "anonymized" datasets.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CASE STUDY — Netflix Prize Re-Identification (2008)</span>
    <span style="background:#ef4444;color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;font-weight:600;">Privacy Failure</span>
  </div>
  <div style="padding:20px;">
    <p style="color:#e5e7eb;margin:0 0 12px;line-height:1.8;">Netflix released a dataset of 100 million movie ratings for a public machine learning competition. They removed all names and replaced user IDs with random numbers. Researchers Narayanan and Shmatikoff showed that by cross-referencing the anonymized ratings with publicly available IMDb reviews, they could re-identify specific individuals — including their political leanings and other sensitive inferences — with high confidence.</p>
    <p style="color:#e5e7eb;margin:0;line-height:1.8;"><strong style="color:#f59e0b;">Lesson:</strong> Anonymization by identifier removal is almost never sufficient in practice. The combination of behavioural patterns — what you rated, when, and how — forms a unique fingerprint even without a name attached.</p>
  </div>
</div>

<h3>Pseudonymisation vs. True Anonymisation</h3>
<p><strong>Pseudonymisation</strong> replaces direct identifiers with artificial codes, but the link between the code and the real person still exists (even if it is stored separately). The data remains personal data under GDPR. <strong>True anonymisation</strong> irreversibly severs any link to a real person, such that re-identification is not reasonably possible — even by the organisation holding the data. Truly anonymised data falls outside GDPR's scope, but achieving genuine anonymisation is technically very difficult, particularly with rich behavioural datasets.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '18.2 Personal Data: Definitions, Types & Sensitivity',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L18_2', [
                ['q' => 'Under the GDPR definition, what makes a piece of information "personal data"?', 'opts' => ['It contains a person\'s full name', 'It contains a national ID number', 'It relates to an identified or identifiable natural person — directly or indirectly', 'It was collected with explicit consent'], 'ans' => 2, 'exp' => 'GDPR defines personal data as any information relating to an identified or *identifiable* natural person. The key word is identifiable — data without a name can still be personal data if it can be linked back to a real person through combination with other information.'],
                ['q' => 'Which category of data receives the strongest legal protection under GDPR Article 9?', 'opts' => ['Email addresses and phone numbers', 'Financial transaction records', 'Special category data including health, biometric, racial/ethnic origin, and sexual orientation data', 'Location and GPS data'], 'ans' => 2, 'exp' => 'GDPR Article 9 designates "special categories" of sensitive data — health, biometric, genetic, racial/ethnic origin, political opinions, religious beliefs, trade union membership, sex life and sexual orientation — that require explicit consent and a stricter legal basis to process.'],
                ['q' => 'Research by Latanya Sweeney showed that what three quasi-identifiers can uniquely identify 87% of Americans?', 'opts' => ['Name, employer, and salary', 'Zip code, date of birth, and gender', 'IP address, device ID, and browsing history', 'Social security number, address, and phone number'], 'ans' => 1, 'exp' => 'Sweeney\'s landmark study demonstrated that the combination of zip code, date of birth, and gender — none of which are directly identifying alone — is sufficient to uniquely identify 87% of US residents. This is the foundational demonstration of the re-identification threat.'],
                ['q' => 'What is the key difference between pseudonymisation and true anonymisation?', 'opts' => ['Pseudonymisation is faster to implement', 'In pseudonymisation, a link between the code and the real person still exists; true anonymisation irreversibly severs that link', 'Pseudonymised data is not subject to any regulation', 'True anonymisation only removes the name field'], 'ans' => 1, 'exp' => 'Pseudonymised data has identifiers replaced by codes, but the mapping still exists somewhere — meaning re-identification is possible. Truly anonymised data has had all linkages permanently severed. Only truly anonymised data falls outside GDPR\'s scope; pseudonymised data is still personal data.'],
                ['q' => 'How were users re-identified in the Netflix Prize dataset despite having their names removed?', 'opts' => ['By hacking Netflix\'s internal database', 'By cross-referencing the anonymized rating patterns with publicly available IMDb reviews, exploiting the uniqueness of individual viewing behaviour', 'By using the user\'s IP address embedded in the dataset', 'By guessing based on the films rated'], 'ans' => 1, 'exp' => 'Narayanan and Shmatikoff showed that the pattern of what films a user rated, when, and with what score forms a unique behavioural fingerprint. Matching this fingerprint against public IMDb reviews allowed re-identification of specific individuals, proving that removing names is insufficient.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 18.3 — Consent, Legal Basis & the GDPR Framework
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Consent, Legal Basis & the GDPR Framework</h2>
<p>The <strong>General Data Protection Regulation (GDPR)</strong>, which came into force in May 2018, is the world's most comprehensive and influential personal data protection law. It applies to any organisation that processes the personal data of EU residents — regardless of where that organisation is located. A startup in the Philippines that has European users must comply with GDPR. A US university that runs a study including French participants must comply with GDPR. Its reach is truly global.</p>

<p>Understanding GDPR is not just a legal compliance exercise — it is a masterclass in thinking systematically about data rights and organisational responsibility. The principles embedded in GDPR represent the current global consensus on what responsible data handling looks like.</p>

<h3>The Seven GDPR Principles (Article 5)</h3>
<p>Every data processing activity must be consistent with all seven principles. Violating any one of them can constitute a breach, regardless of whether the specific rule was written down.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — GDPR Article 5: The Seven Principles</span>
  </div>
  <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
    <div style="display:flex;gap:14px;align-items:flex-start;">
      <span style="background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:'JetBrains Mono',monospace;white-space:nowrap;flex-shrink:0;">1</span>
      <div><strong style="color:#e5e7eb;">Lawfulness, Fairness & Transparency</strong> <span style="color:#9ca3af;">— Processing must have a valid legal basis, must not deceive or harm data subjects, and must be open about how data is used.</span></div>
    </div>
    <div style="display:flex;gap:14px;align-items:flex-start;">
      <span style="background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:'JetBrains Mono',monospace;white-space:nowrap;flex-shrink:0;">2</span>
      <div><strong style="color:#e5e7eb;">Purpose Limitation</strong> <span style="color:#9ca3af;">— Data collected for one specific purpose cannot be reused for a different, incompatible purpose without a new legal basis. You cannot collect data "to improve service" and then sell it to advertisers.</span></div>
    </div>
    <div style="display:flex;gap:14px;align-items:flex-start;">
      <span style="background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:'JetBrains Mono',monospace;white-space:nowrap;flex-shrink:0;">3</span>
      <div><strong style="color:#e5e7eb;">Data Minimisation</strong> <span style="color:#9ca3af;">— Collect only the data that is <em>adequate, relevant, and limited</em> to what is necessary for the stated purpose. Do not collect data "just in case it might be useful."</span></div>
    </div>
    <div style="display:flex;gap:14px;align-items:flex-start;">
      <span style="background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:'JetBrains Mono',monospace;white-space:nowrap;flex-shrink:0;">4</span>
      <div><strong style="color:#e5e7eb;">Accuracy</strong> <span style="color:#9ca3af;">— Personal data must be kept accurate and up to date. Inaccurate data must be corrected or deleted without delay. Stale data that drives decisions causes real harm.</span></div>
    </div>
    <div style="display:flex;gap:14px;align-items:flex-start;">
      <span style="background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:'JetBrains Mono',monospace;white-space:nowrap;flex-shrink:0;">5</span>
      <div><strong style="color:#e5e7eb;">Storage Limitation</strong> <span style="color:#9ca3af;">— Data must not be kept for longer than necessary for its stated purpose. You must define and enforce data retention periods.</span></div>
    </div>
    <div style="display:flex;gap:14px;align-items:flex-start;">
      <span style="background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:'JetBrains Mono',monospace;white-space:nowrap;flex-shrink:0;">6</span>
      <div><strong style="color:#e5e7eb;">Integrity & Confidentiality (Security)</strong> <span style="color:#9ca3af;">— Data must be protected against unauthorised access, accidental loss, destruction, or damage using appropriate technical and organisational measures.</span></div>
    </div>
    <div style="display:flex;gap:14px;align-items:flex-start;">
      <span style="background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:'JetBrains Mono',monospace;white-space:nowrap;flex-shrink:0;">7</span>
      <div><strong style="color:#e5e7eb;">Accountability</strong> <span style="color:#9ca3af;">— The data controller must be able to <em>demonstrate</em> compliance with all of the above. Compliance must be documented, not merely claimed.</span></div>
    </div>
  </div>
</div>

<h3>The Six Legal Bases for Processing Personal Data</h3>
<p>Every time you process personal data, you must identify and document one of six legal bases. Consent is the most widely known but not always the most appropriate. Organisations often over-rely on consent when a more appropriate basis exists — and this matters because each basis carries different obligations.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — GDPR Article 6: Six Legal Bases</span>
  </div>
  <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
    <div style="border-left:3px solid #10b981;padding-left:14px;">
      <strong style="color:#10b981;">Consent</strong> <span style="color:#9ca3af;">— The individual has freely given, specific, informed, and unambiguous consent. Must be as easy to withdraw as to give. Cannot be bundled into T&amp;Cs or pre-ticked boxes.</span>
    </div>
    <div style="border-left:3px solid #3b82f6;padding-left:14px;">
      <strong style="color:#3b82f6;">Contract</strong> <span style="color:#9ca3af;">— Processing is necessary to fulfil a contract with the individual (e.g., processing a customer's address to deliver an order they placed).</span>
    </div>
    <div style="border-left:3px solid #a78bfa;padding-left:14px;">
      <strong style="color:#a78bfa;">Legal Obligation</strong> <span style="color:#9ca3af;">— Processing is required to comply with a law (e.g., keeping financial records for tax audits, retaining employment data for labour law compliance).</span>
    </div>
    <div style="border-left:3px solid #f59e0b;padding-left:14px;">
      <strong style="color:#f59e0b;">Vital Interests</strong> <span style="color:#9ca3af;">— Processing is necessary to protect someone's life (e.g., sharing medical data in a genuine emergency). A narrow basis rarely applicable outside health contexts.</span>
    </div>
    <div style="border-left:3px solid #ec4899;padding-left:14px;">
      <strong style="color:#ec4899;">Public Task</strong> <span style="color:#9ca3af;">— Processing is necessary to perform a task in the public interest or exercise of official authority (e.g., government statistical offices, public health surveillance).</span>
    </div>
    <div style="border-left:3px solid #ef4444;padding-left:14px;">
      <strong style="color:#ef4444;">Legitimate Interests</strong> <span style="color:#9ca3af;">— The organisation has a legitimate interest that is not overridden by the individual's privacy rights. Requires a documented balancing test. The most flexible but most frequently abused basis.</span>
    </div>
  </div>
</div>

<h3>Individual Rights Under GDPR</h3>
<p>GDPR creates eight specific rights that data subjects can exercise against any organisation processing their data. Building systems that can honour these rights is a core engineering and governance responsibility:</p>
<ul style="line-height:2.4;color:#e5e7eb;">
  <li><strong>Right of Access (Article 15)</strong> — Request a copy of all personal data an organisation holds about them.</li>
  <li><strong>Right to Rectification (Article 16)</strong> — Have inaccurate personal data corrected.</li>
  <li><strong>Right to Erasure / "Right to Be Forgotten" (Article 17)</strong> — Have personal data deleted under certain conditions.</li>
  <li><strong>Right to Restrict Processing (Article 18)</strong> — Limit how their data is used while a dispute is resolved.</li>
  <li><strong>Right to Data Portability (Article 20)</strong> — Receive their data in a machine-readable format and transfer it to another provider.</li>
  <li><strong>Right to Object (Article 21)</strong> — Object to processing based on legitimate interests or for direct marketing.</li>
  <li><strong>Rights Related to Automated Decision-Making (Article 22)</strong> — Not be subject to decisions made solely by automated processing that significantly affect them, without human review.</li>
  <li><strong>Right to Withdraw Consent</strong> — Withdraw previously given consent at any time, as easily as it was given.</li>
</ul>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '18.3 Consent, Legal Basis & the GDPR Framework',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L18_3', [
                ['q' => 'Which GDPR principle prohibits collecting data "just in case it might be useful someday"?', 'opts' => ['Purpose Limitation', 'Data Minimisation', 'Accuracy', 'Storage Limitation'], 'ans' => 1, 'exp' => 'Data Minimisation (Article 5(1)(c)) requires that personal data be adequate, relevant, and limited to what is necessary for the stated purpose. Collecting speculative or precautionary data without a clear, defined purpose violates this principle.'],
                ['q' => 'Under GDPR, what are the requirements for valid consent to be lawful?', 'opts' => ['The user must click a button anywhere on the website', 'Consent must be freely given, specific, informed, and unambiguous — and as easy to withdraw as to give', 'Consent can be implied by continued use of a service', 'A pre-ticked checkbox in terms and conditions is sufficient'], 'ans' => 1, 'exp' => 'Valid GDPR consent must be: freely given (no coercion), specific (for a defined purpose), informed (the person understands what they are consenting to), and unambiguous (a clear affirmative action — no pre-ticked boxes). It must also be as easy to withdraw as to give.'],
                ['q' => 'A data subject exercises their "Right to Erasure" under GDPR Article 17. What does this mean for an organisation?', 'opts' => ['They must archive the data in cold storage', 'They must delete the individual\'s personal data under certain conditions, such as when the original purpose has been fulfilled', 'They must encrypt the data but may retain it', 'They must restrict access to the data to senior staff only'], 'ans' => 1, 'exp' => 'The Right to Erasure (Right to Be Forgotten) allows individuals to request deletion of their personal data in certain circumstances — such as when the data is no longer necessary for its original purpose, or when consent is withdrawn. Organisations must have processes to honour this request.'],
                ['q' => 'Which GDPR Article gives individuals the right to not be subject to decisions made solely by automated processing that significantly affect them?', 'opts' => ['Article 5 — Data Minimisation', 'Article 15 — Right of Access', 'Article 22 — Automated Decision-Making', 'Article 17 — Right to Erasure'], 'ans' => 2, 'exp' => 'Article 22 gives individuals the right not to be subject to solely automated decisions with significant effects (like loan approvals, job applications, or criminal profiling) without human review. This is directly relevant to machine learning models deployed in high-stakes settings.'],
                ['q' => 'The GDPR applies to an organisation that is based in the Philippines but processes personal data of EU residents. Is this correct?', 'opts' => ['No — GDPR only applies to organisations located within the EU', 'No — GDPR only applies to EU-based data subjects who are EU citizens', 'Yes — GDPR applies to any organisation that processes the personal data of EU residents, regardless of where the organisation is located', 'Yes — but only if the organisation has a physical office in an EU country'], 'ans' => 2, 'exp' => 'GDPR has extraterritorial reach (Article 3). It applies to any organisation anywhere in the world that processes the personal data of individuals located in the EU, regardless of where the organisation itself is based. This is one reason GDPR has become a de facto global standard.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 18.4 — Global Privacy Laws: CCPA, PDPA & Beyond
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Global Privacy Laws: CCPA, PDPA & Beyond</h2>
<p>GDPR is the most widely studied privacy law, but it is far from the only one. The past decade has seen an explosion of privacy legislation around the world, driven by growing public concern about surveillance capitalism, data breaches, and the unchecked power of technology platforms. As a data scientist working in a global environment, you must understand the legal landscape of the jurisdictions you operate in — because the penalties for non-compliance are severe and the rules are not identical.</p>

<h3>The United States: California Consumer Privacy Act (CCPA / CPRA)</h3>
<p>The United States has no single federal privacy law equivalent to GDPR. Instead, it has a patchwork of sector-specific laws (HIPAA for health, FERPA for education, COPPA for children) and state-level privacy legislation. The most significant is the <strong>California Consumer Privacy Act (CCPA)</strong>, significantly expanded by the California Privacy Rights Act (CPRA) effective January 2023.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — CCPA/CPRA Key Rights & Obligations</span>
  </div>
  <div style="padding:20px;display:flex;flex-direction:column;gap:16px;">
    <div style="border-left:3px solid #3b82f6;padding-left:16px;">
      <strong style="color:#3b82f6;">Right to Know</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Consumers can request disclosure of what personal information a business collects, uses, discloses, and sells about them.</p>
    </div>
    <div style="border-left:3px solid #10b981;padding-left:16px;">
      <strong style="color:#10b981;">Right to Delete</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Consumers can request deletion of personal information a business has collected, subject to certain exceptions (e.g., legal obligations, completing transactions).</p>
    </div>
    <div style="border-left:3px solid #f59e0b;padding-left:16px;">
      <strong style="color:#f59e0b;">Right to Opt-Out of Sale</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Consumers can direct a business not to sell or share their personal information. This is broader than GDPR's equivalent — "sharing" for cross-context behavioural advertising counts as a sale under CCPA.</p>
    </div>
    <div style="border-left:3px solid #a78bfa;padding-left:16px;">
      <strong style="color:#a78bfa;">Right to Non-Discrimination</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Businesses cannot deny goods or services, charge different prices, or provide a different level of service to consumers who exercise their privacy rights.</p>
    </div>
    <div style="border-left:3px solid #ef4444;padding-left:16px;">
      <strong style="color:#ef4444;">Sensitive Personal Information Controls (CPRA addition)</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">The CPRA created a special category of sensitive personal information (similar to GDPR's Article 9) that consumers can limit the use of, including precise geolocation, racial/ethnic origin, health data, and more.</p>
    </div>
  </div>
</div>

<h3>Southeast Asia: The Philippine Data Privacy Act (DPA) of 2012</h3>
<p>The <strong>Philippine Data Privacy Act of 2012 (Republic Act 10173)</strong> is the primary data protection law of the Philippines, enforced by the <strong>National Privacy Commission (NPC)</strong>. It applies to the processing of personal information by government and private entities. For data scientists working in or with Filipino organisations, this is the primary framework:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — Philippine DPA Key Provisions</span>
  </div>
  <div style="padding:20px;display:flex;flex-direction:column;gap:16px;">
    <div style="border-left:3px solid #10b981;padding-left:16px;">
      <strong style="color:#10b981;">Data Privacy Principles</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">The DPA requires that personal information processing be: <em>Transparent</em> (individuals know how their data is used), <em>Legitimate</em> (processing has a valid legal basis), and <em>Proportionate</em> (data collected is only what is necessary).</p>
    </div>
    <div style="border-left:3px solid #3b82f6;padding-left:16px;">
      <strong style="color:#3b82f6;">Data Subject Rights</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Philippine data subjects have: the right to be informed, right to access, right to object, right to erasure/blocking, right to damages, and the right to file a complaint with the NPC.</p>
    </div>
    <div style="border-left:3px solid #f59e0b;padding-left:16px;">
      <strong style="color:#f59e0b;">Data Protection Officer (DPO)</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Organisations that process personal data must appoint a Data Protection Officer, register with the NPC if processing sensitive personal information, and maintain a Privacy Management Program.</p>
    </div>
    <div style="border-left:3px solid #ef4444;padding-left:16px;">
      <strong style="color:#ef4444;">Breach Notification</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Personal data breaches must be reported to the NPC within 72 hours of knowledge of the breach (aligned with GDPR). Affected data subjects must be notified if the breach is likely to result in significant harm.</p>
    </div>
    <div style="border-left:3px solid #a78bfa;padding-left:16px;">
      <strong style="color:#a78bfa;">Penalties</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Violations can result in imprisonment of 1–6 years and fines of ₱500,000–₱5,000,000. Unauthorized processing of sensitive personal information carries the highest penalties.</p>
    </div>
  </div>
</div>

<h3>Comparative Overview: Key Laws at a Glance</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">REFERENCE — Global Privacy Laws Comparison</span>
  </div>
  <div style="padding:20px;overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;color:#e5e7eb;font-size:0.85rem;">
      <thead>
        <tr style="border-bottom:2px solid var(--border);">
          <th style="text-align:left;padding:10px 12px;color:#9ca3af;font-weight:600;">Law</th>
          <th style="text-align:left;padding:10px 12px;color:#9ca3af;font-weight:600;">Jurisdiction</th>
          <th style="text-align:left;padding:10px 12px;color:#9ca3af;font-weight:600;">Max Fine</th>
          <th style="text-align:left;padding:10px 12px;color:#9ca3af;font-weight:600;">Breach Notification</th>
        </tr>
      </thead>
      <tbody>
        <tr style="border-bottom:1px solid var(--border);">
          <td style="padding:10px 12px;font-weight:600;color:#a7f3d0;">GDPR (EU)</td>
          <td style="padding:10px 12px;">EU + extraterritorial</td>
          <td style="padding:10px 12px;">€20M or 4% global revenue</td>
          <td style="padding:10px 12px;">72 hours to authority</td>
        </tr>
        <tr style="border-bottom:1px solid var(--border);">
          <td style="padding:10px 12px;font-weight:600;color:#93c5fd;">CCPA/CPRA (US-CA)</td>
          <td style="padding:10px 12px;">California residents</td>
          <td style="padding:10px 12px;">$7,500 per intentional violation</td>
          <td style="padding:10px 12px;">Prompt notification</td>
        </tr>
        <tr style="border-bottom:1px solid var(--border);">
          <td style="padding:10px 12px;font-weight:600;color:#fca5a5;">DPA (Philippines)</td>
          <td style="padding:10px 12px;">Philippines</td>
          <td style="padding:10px 12px;">₱5M + 6 years imprisonment</td>
          <td style="padding:10px 12px;">72 hours to NPC</td>
        </tr>
        <tr style="border-bottom:1px solid var(--border);">
          <td style="padding:10px 12px;font-weight:600;color:#fcd34d;">PDPA (Thailand)</td>
          <td style="padding:10px 12px;">Thailand</td>
          <td style="padding:10px 12px;">THB 5M + criminal penalties</td>
          <td style="padding:10px 12px;">72 hours to authority</td>
        </tr>
        <tr>
          <td style="padding:10px 12px;font-weight:600;color:#d8b4fe;">PIPL (China)</td>
          <td style="padding:10px 12px;">China + extraterritorial</td>
          <td style="padding:10px 12px;">¥50M or 5% annual revenue</td>
          <td style="padding:10px 12px;">Immediate notification</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '18.4 Global Privacy Laws: CCPA, PDPA & Beyond',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L18_4', [
                ['q' => 'What is unique about the CCPA\'s definition of "sale" of personal data compared to everyday understanding?', 'opts' => ['It only covers monetary transactions above $500', 'It includes sharing personal data for cross-context behavioural advertising — even if no money changes hands', 'It only applies to health and financial data', 'It requires the consumer to have made a prior purchase from the business'], 'ans' => 1, 'exp' => 'CCPA\'s definition of "selling" personal data extends beyond cash transactions to include sharing data for cross-context behavioural advertising — meaning that sharing data with ad-tech companies to target users counts as a sale, and consumers have the right to opt out of it.'],
                ['q' => 'Which Philippine government body enforces the Data Privacy Act of 2012?', 'opts' => ['The Department of Information and Communications Technology (DICT)', 'The National Privacy Commission (NPC)', 'The Securities and Exchange Commission (SEC)', 'The Data Protection Bureau under the Department of Justice'], 'ans' => 1, 'exp' => 'The National Privacy Commission (NPC) is the independent body created under the Philippine DPA to administer and enforce the Act, receive breach notifications, investigate complaints, and issue guidance to organisations.'],
                ['q' => 'Under the Philippine DPA, within how many hours must a data breach be reported to the National Privacy Commission?', 'opts' => ['24 hours', '48 hours', '72 hours', '7 days'], 'ans' => 2, 'exp' => 'The Philippine DPA (aligned with GDPR) requires breach notification to the NPC within 72 hours of knowledge of the breach when it is likely to result in significant harm to data subjects. This same 72-hour window appears in both GDPR and the Philippine rules.'],
                ['q' => 'A company based in Tokyo operates an e-commerce website that serves customers in France. Which privacy law(s) must it comply with when handling French customers\' data?', 'opts' => ['Only Japan\'s Act on the Protection of Personal Information (APPI)', 'Only GDPR — because the customers are in France', 'Both GDPR (due to the customers\' location in the EU) and Japan\'s APPI (due to the company\'s location)', 'Neither — it is an international e-commerce context with no clear jurisdiction'], 'ans' => 2, 'exp' => 'A Japanese company serving EU customers must comply with GDPR due to GDPR\'s extraterritorial scope (Article 3), which applies to any organisation that processes EU residents\' data regardless of where it is located. It must also comply with Japan\'s APPI as the law of its home jurisdiction.'],
                ['q' => 'Which of these privacy laws carries the highest possible financial penalty as a percentage of global revenue?', 'opts' => ['CCPA/CPRA — $7,500 per violation', 'Philippine DPA — ₱5 million', 'GDPR — 4% of total worldwide annual turnover', 'China\'s PIPL — 5% of annual revenue'], 'ans' => 2, 'exp' => 'GDPR\'s maximum fine of €20 million or 4% of total worldwide annual turnover (whichever is higher) is the most widely cited. However, PIPL\'s 5% of annual revenue is technically higher on a percentage basis — demonstrating that GDPR is not necessarily the strictest law globally.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 18.5 — Algorithmic Bias: Sources, Types & Detection
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Algorithmic Bias: Sources, Types & Detection</h2>
<p><strong>Algorithmic bias</strong> occurs when a machine learning system produces outputs that are systematically and unjustifiably different for different groups of people — particularly groups defined by protected characteristics like race, gender, age, religion, or disability. Bias is not a rare failure mode that only happens to careless engineers: it is the default outcome when data and modelling choices are made without deliberate fairness consideration. Understanding its sources and types is the first step to addressing it.</p>

<h3>Where Does Bias Come From?</h3>
<p>Bias can enter a machine learning pipeline at every stage — from how the problem is framed to how the model is deployed. It is not a single switch that gets flipped; it is a series of small decisions that accumulate.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — Sources of Algorithmic Bias in the ML Pipeline</span>
  </div>
  <div style="padding:20px;display:flex;flex-direction:column;gap:16px;">
    <div style="border-left:3px solid #ef4444;padding-left:16px;">
      <strong style="color:#ef4444;">Historical Bias</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">The real world contains historical inequalities that data reflects. Training a model on past hiring decisions, past loan approvals, or past arrest records does not produce a neutral system — it produces one that perpetuates historical discrimination. The model does exactly what it was trained to do: replicate the past.</p>
    </div>
    <div style="border-left:3px solid #f59e0b;padding-left:16px;">
      <strong style="color:#f59e0b;">Representation Bias (Sampling Bias)</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">The training data does not adequately represent all groups the model will be applied to. A facial recognition system trained primarily on light-skinned faces will perform worse on dark-skinned faces — not because of malice, but because the training data was not representative. Underrepresented groups receive lower quality predictions.</p>
    </div>
    <div style="border-left:3px solid #a78bfa;padding-left:16px;">
      <strong style="color:#a78bfa;">Measurement Bias</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">The way data is measured or labelled is systematically different across groups. Example: using "number of prior arrests" as a proxy for criminality introduces bias because policing intensity varies enormously by neighbourhood — so "arrested" is not a neutral measure of criminal behaviour; it partly measures how much police attention a community receives.</p>
    </div>
    <div style="border-left:3px solid #3b82f6;padding-left:16px;">
      <strong style="color:#3b82f6;">Aggregation Bias</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">A single model trained on a diverse population may perform well on average but poorly for specific subgroups. If a medical model is trained to predict diabetes progression across a diverse population, it may produce accurate average predictions while systematically over- or under-predicting for specific ethnic groups whose physiological patterns differ from the majority.</p>
    </div>
    <div style="border-left:3px solid #10b981;padding-left:16px;">
      <strong style="color:#10b981;">Deployment Bias</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">The model is used in a context or for a purpose that differs from what it was designed for. A natural language processing model trained on English text performs poorly on Filipino or Tagalog — but if deployed in a multilingual context without this limitation being clearly communicated, the result is unequal service quality.</p>
    </div>
  </div>
</div>

<h3>The COMPAS Case Study: Predictive Policing & Recidivism</h3>
<p>COMPAS (Correctional Offender Management Profiling for Alternative Sanctions) is an algorithmic risk-assessment tool used by US courts to predict the likelihood that a defendant will reoffend. Judges use its risk scores to inform bail, sentencing, and parole decisions. A 2016 investigation by ProPublica found that the system was far more likely to falsely flag Black defendants as high-risk compared to white defendants, while white defendants who actually reoffended were more often labelled low-risk.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CASE STUDY — COMPAS Recidivism Algorithm</span>
    <span style="background:#ef4444;color:#fff;padding:6px 12px;border-radius:4px;font-size:0.75rem;font-weight:600;">Algorithmic Bias</span>
  </div>
  <div style="padding:20px;">
    <p style="color:#e5e7eb;margin:0 0 12px;line-height:1.8;"><strong style="color:#f59e0b;">ProPublica finding:</strong> Black defendants who did not reoffend were nearly twice as likely (45% vs 24%) to be falsely classified as high-risk compared to white defendants. White defendants who did reoffend were more likely to be classified as low-risk.</p>
    <p style="color:#e5e7eb;margin:0 0 12px;line-height:1.8;"><strong style="color:#3b82f6;">The company's response:</strong> Northpointe (COMPAS's maker) argued the tool was fair because it achieved equal <em>predictive accuracy</em> across racial groups. Both sides were right — this exposed a fundamental mathematical impossibility: you cannot simultaneously satisfy all common definitions of fairness when base rates differ across groups.</p>
    <p style="color:#e5e7eb;margin:0;line-height:1.8;"><strong style="color:#10b981;">Why it matters:</strong> This is not an abstract academic debate. COMPAS scores influence how long human beings spend in prison. It is one of the most studied examples of what happens when algorithmic outputs carry life-altering consequences without adequate bias auditing.</p>
  </div>
</div>

<h3>Fairness Cannot Be Defined by a Single Metric</h3>
<p>One of the most important — and counterintuitive — results in algorithmic fairness research is that the most common definitions of "fair" are <strong>mathematically incompatible</strong> with each other when a model is applied to populations with different base rates. You cannot simultaneously have:</p>
<ul style="line-height:2.2;color:#e5e7eb;">
  <li><strong>Calibration</strong> — the predicted probability of recidivism means the same thing for all groups</li>
  <li><strong>Equal False Positive Rates</strong> — non-reoffenders are wrongly labelled high-risk at the same rate across groups</li>
  <li><strong>Equal False Negative Rates</strong> — reoffenders are wrongly labelled low-risk at the same rate across groups</li>
</ul>
<p>This is not a flaw in any particular tool — it is a mathematical theorem (the Chouldechova impossibility result). This means that choosing a fairness criterion is inherently a <em>value choice</em>, not a technical one. It demands human judgment about which type of error is more harmful for which group — a deeply ethical question that no algorithm can answer for you.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '18.5 Algorithmic Bias: Sources, Types & Detection',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L18_5', [
                ['q' => 'A facial recognition system trained mostly on light-skinned faces performs significantly worse on dark-skinned faces. Which type of bias best describes this?', 'opts' => ['Historical Bias — because society has historically discriminated against dark-skinned people', 'Representation Bias — the training data does not adequately represent all groups the model will be applied to', 'Deployment Bias — the model is being used in the wrong context', 'Aggregation Bias — the model performs well on average but poorly for subgroups'], 'ans' => 1, 'exp' => 'Representation (sampling) bias occurs when the training dataset does not proportionally include all groups the model will encounter. If the training data was predominantly light-skinned faces, the model will have learned detailed patterns for those faces but coarse, imprecise patterns for darker-skinned faces — resulting in systematically worse performance.'],
                ['q' => 'Using "number of prior arrests" as a proxy for criminality in an ML model introduces which type of bias?', 'opts' => ['Aggregation Bias — mixing different population groups', 'Historical Bias — arrests reflect past discrimination', 'Measurement Bias — the measure is systematically different across groups because policing intensity varies by neighbourhood', 'Deployment Bias — the model is used in a different context than intended'], 'ans' => 2, 'exp' => 'Measurement bias occurs when a feature does not measure the same underlying concept equally across groups. "Prior arrests" partly measures criminal behaviour and partly measures how much police attention a community receives. Since policing intensity varies systematically by race and neighbourhood, this measure is not a neutral proxy.'],
                ['q' => 'What was the key finding of ProPublica\'s 2016 analysis of the COMPAS recidivism algorithm?', 'opts' => ['The algorithm was 100% inaccurate for all defendants', 'Black defendants who did not reoffend were nearly twice as likely as white non-reoffenders to be falsely labelled high-risk by the algorithm', 'The algorithm consistently underestimated reoffending risk for all groups', 'The algorithm had a random error rate that was not related to race'], 'ans' => 1, 'exp' => 'ProPublica found that COMPAS had drastically different false positive rates by race: 45% of Black defendants who did not reoffend were labelled high-risk, compared to 24% of white defendants who did not reoffend — nearly double the false alarm rate for Black defendants.'],
                ['q' => 'What does the Chouldechova impossibility result demonstrate about algorithmic fairness?', 'opts' => ['All fairness criteria can be satisfied simultaneously with enough data', 'When base rates differ across groups, common definitions of fairness are mathematically incompatible — you cannot satisfy all of them at once', 'Algorithms are always biased and should not be used for high-stakes decisions', 'Fairness can only be measured using accuracy'], 'ans' => 1, 'exp' => 'Chouldechova\'s impossibility theorem proves that when base rates (e.g., actual reoffending rates) differ across groups, you cannot simultaneously achieve calibration, equal false positive rates, and equal false negative rates. Choosing which fairness criterion to prioritise is therefore a value judgment, not a technical calculation.'],
                ['q' => 'A hospital trains a single diabetes progression model on a diverse mixed-ethnicity dataset. It achieves 85% accuracy overall but performs significantly worse for one ethnic group. What type of bias is this?', 'opts' => ['Historical Bias', 'Representation Bias', 'Aggregation Bias', 'Measurement Bias'], 'ans' => 2, 'exp' => 'Aggregation bias occurs when a single model is trained across heterogeneous groups whose underlying patterns differ. The model learns a "blended" average pattern that may be accurate for the majority but systematically fails for subgroups whose physiology or context differs from the average the model learned.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 18.6 — Fairness Frameworks & Mitigation Strategies
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Fairness Frameworks & Mitigation Strategies</h2>
<p>Knowing that bias exists is only half the battle. The other half is doing something about it — in a principled, documented, and defensible way. This lesson presents the major technical frameworks for defining and measuring fairness, and the practical strategies for reducing bias at each stage of the ML pipeline. None of these are silver bullets: bias mitigation requires ongoing vigilance, not one-time fixes.</p>

<h3>Defining Fairness: The Major Technical Criteria</h3>
<p>There are over 20 published definitions of algorithmic fairness. The most practically important ones fall into three categories:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — The Three Fairness Criteria Categories</span>
  </div>
  <div style="padding:20px;display:flex;flex-direction:column;gap:20px;">
    <div>
      <p style="color:#10b981;font-weight:700;margin:0 0 8px;font-size:0.9rem;">Individual Fairness</p>
      <p style="color:#e5e7eb;margin:0 0 8px;line-height:1.7;"><strong>Definition:</strong> Similar individuals should receive similar predictions. Two people who are equally qualified for a loan should receive the same risk score, regardless of their race or gender.</p>
      <p style="color:#9ca3af;margin:0;line-height:1.7;"><strong>Challenge:</strong> Requires defining "similar" — which itself can embed assumptions about which features matter.</p>
    </div>
    <div style="border-top:1px solid var(--border);padding-top:20px;">
      <p style="color:#3b82f6;font-weight:700;margin:0 0 8px;font-size:0.9rem;">Group Fairness (Statistical Parity)</p>
      <p style="color:#e5e7eb;margin:0 0 8px;line-height:1.7;"><strong>Demographic Parity:</strong> The model's positive prediction rate is equal across protected groups. If 30% of men are approved for loans, 30% of women should also be approved.</p>
      <p style="color:#e5e7eb;margin:0 0 8px;line-height:1.7;"><strong>Equalised Odds:</strong> Both the true positive rate AND false positive rate are equal across groups. More demanding than demographic parity — requires matching error rates, not just approval rates.</p>
      <p style="color:#9ca3af;margin:0;line-height:1.7;"><strong>Challenge:</strong> Demographic parity may require approving less-qualified candidates from underrepresented groups to match numbers — which trades one type of unfairness for another.</p>
    </div>
    <div style="border-top:1px solid var(--border);padding-top:20px;">
      <p style="color:#f59e0b;font-weight:700;margin:0 0 8px;font-size:0.9rem;">Calibration (Predictive Parity)</p>
      <p style="color:#e5e7eb;margin:0 0 8px;line-height:1.7;"><strong>Definition:</strong> Among all individuals given a predicted probability of 70%, approximately 70% should actually experience the predicted outcome — equally across groups. The score means the same thing regardless of who it is applied to.</p>
      <p style="color:#9ca3af;margin:0;line-height:1.7;"><strong>Challenge:</strong> As the Chouldechova theorem shows, calibration is mathematically incompatible with equalised odds when base rates differ across groups.</p>
    </div>
  </div>
</div>

<h3>The Three-Stage Bias Mitigation Approach</h3>
<p>Bias mitigation can be applied at three points in the ML pipeline. Each has advantages and limitations:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — Pre-, In-, and Post-Processing Interventions</span>
  </div>
  <div style="padding:20px;display:flex;flex-direction:column;gap:20px;">
    <div style="background:rgba(16,185,129,0.06);border:1px solid rgba(16,185,129,0.2);border-radius:8px;padding:16px;">
      <p style="color:#10b981;font-weight:700;margin:0 0 8px;text-transform:uppercase;font-size:0.75rem;letter-spacing:0.05em;">Pre-Processing (Fix the Data)</p>
      <p style="color:#e5e7eb;margin:0 0 8px;line-height:1.7;">Modify the training data before the model sees it. Techniques: re-sampling to balance group representation, re-weighting training examples, removing proxy variables (features that correlate strongly with protected attributes), and relabelling to correct historical bias in labels.</p>
      <p style="color:#9ca3af;margin:0;font-size:0.85rem;"><strong>Best for:</strong> When bias sources are well understood and data-level fixes are feasible. Does not require changes to the model itself.</p>
    </div>
    <div style="background:rgba(59,130,246,0.06);border:1px solid rgba(59,130,246,0.2);border-radius:8px;padding:16px;">
      <p style="color:#3b82f6;font-weight:700;margin:0 0 8px;text-transform:uppercase;font-size:0.75rem;letter-spacing:0.05em;">In-Processing (Constrain the Model)</p>
      <p style="color:#e5e7eb;margin:0 0 8px;line-height:1.7;">Add fairness constraints directly to the model's training objective — penalise the model when predictions differ unfairly across groups. Libraries like IBM's AI Fairness 360 and Google's TensorFlow Constrained Optimization implement this. The model must now satisfy both accuracy and fairness objectives simultaneously.</p>
      <p style="color:#9ca3af;margin:0;font-size:0.85rem;"><strong>Best for:</strong> When you have control over the training process and need fairness guarantees during learning. Often trades some accuracy for fairness — the fairness-accuracy tradeoff.</p>
    </div>
    <div style="background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.2);border-radius:8px;padding:16px;">
      <p style="color:#ef4444;font-weight:700;margin:0 0 8px;text-transform:uppercase;font-size:0.75rem;letter-spacing:0.05em;">Post-Processing (Adjust the Outputs)</p>
      <p style="color:#e5e7eb;margin:0 0 8px;line-height:1.7;">After the model is trained, adjust its predictions to satisfy a chosen fairness criterion. Techniques include threshold calibration (using different decision thresholds for different groups) and reject-option classification (allowing human review for borderline predictions near the decision boundary).</p>
      <p style="color:#9ca3af;margin:0;font-size:0.85rem;"><strong>Best for:</strong> When you cannot modify the model or data (e.g., third-party models). Easiest to implement but does not address root causes.</p>
    </div>
  </div>
</div>

<h3>Fairness Auditing: The Ongoing Responsibility</h3>
<p>A fairness intervention at deployment time is not a permanent fix. Model performance and fairness properties can drift as the population changes, as the feature distribution shifts, or as the world itself changes. <strong>Ongoing fairness auditing</strong> — regular disaggregated evaluation of model performance across protected groups — is a continuous operational responsibility, not a one-time checkbox. A model that was fair when deployed in January may be measurably biased by June if the data distribution has shifted.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '18.6 Fairness Frameworks & Mitigation Strategies',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L18_6', [
                ['q' => 'A loan model approves 40% of male applicants and 40% of female applicants. Which fairness criterion does this satisfy?', 'opts' => ['Calibration (Predictive Parity)', 'Individual Fairness', 'Demographic Parity (Statistical Parity)', 'Equalised Odds'], 'ans' => 2, 'exp' => 'Demographic Parity requires that the positive prediction rate (approval rate) be equal across protected groups. If 40% of both male and female applicants are approved, the approval rate is equal across gender groups — satisfying demographic parity.'],
                ['q' => 'What is the fairness-accuracy tradeoff in in-processing mitigation?', 'opts' => ['Accurate models are always fairer than less accurate ones', 'Adding fairness constraints to the training objective can reduce the model\'s overall predictive accuracy because it is optimising for two objectives simultaneously', 'Fairness constraints always improve accuracy on underrepresented groups', 'In-processing only applies to classification models, not regression'], 'ans' => 1, 'exp' => 'When you constrain a model to satisfy fairness requirements during training, you force it to optimise for both accuracy and fairness simultaneously. These objectives may conflict, particularly if the training data reflects historical bias — resulting in slightly lower overall accuracy in exchange for fairer predictions across groups.'],
                ['q' => 'Which bias mitigation approach is best suited when you are using a third-party pre-trained model you cannot modify?', 'opts' => ['Pre-processing — re-sampling the training data', 'In-processing — adding constraints to the training loop', 'Post-processing — adjusting the model\'s outputs after prediction using threshold calibration', 'None — third-party models cannot be audited or adjusted'], 'ans' => 2, 'exp' => 'Post-processing techniques work on model outputs after prediction — they do not require access to the model\'s internal structure or training process. This makes them the only option when using a third-party black-box model you cannot modify.'],
                ['q' => 'Why is a one-time fairness audit at deployment insufficient?', 'opts' => ['Because audits are too expensive to be done only once', 'Because model performance and fairness properties can drift as population distributions, feature distributions, and societal conditions change over time', 'Because regulators require audits every 6 months by law', 'Because fairness criteria change with every model update'], 'ans' => 1, 'exp' => 'Model fairness can degrade after deployment due to distribution shift — changes in the population being served, economic conditions, or even upstream data pipelines. Ongoing disaggregated monitoring (evaluating performance separately for each protected group) is required to catch these drifts before they cause harm.'],
                ['q' => 'What does "Equalised Odds" require compared to simple Demographic Parity?', 'opts' => ['It only requires equal approval rates across groups', 'It requires both the true positive rate and the false positive rate to be equal across protected groups — matching error rates, not just outcome rates', 'It requires the model to use identical features for all groups', 'It requires removing all protected attributes from the training data'], 'ans' => 1, 'exp' => 'Demographic Parity only requires equal positive prediction rates. Equalised Odds is more demanding: it requires both the True Positive Rate (recall) and the False Positive Rate to be equal across groups. This means the model must make errors at the same rate for all groups, not just approve people at the same rate.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 18.7 — Privacy-Enhancing Technologies (PETs)
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Privacy-Enhancing Technologies (PETs)</h2>
<p><strong>Privacy-Enhancing Technologies (PETs)</strong> are technical tools and approaches that allow organisations to extract value from data while minimising privacy risks. The traditional view treated privacy and data utility as a binary tradeoff: either you share data and lose privacy, or you protect privacy and lose utility. PETs challenge this assumption — they allow useful analysis on sensitive data without requiring full exposure of the underlying information.</p>

<p>As a data scientist, understanding PETs equips you to propose privacy-respecting alternatives to raw data sharing, contribute to privacy-by-design architectures, and have informed conversations with legal and security teams about the technical feasibility of privacy-protective approaches.</p>

<h3>Differential Privacy: Mathematical Privacy Guarantees</h3>
<p><strong>Differential Privacy (DP)</strong> is a mathematically rigorous framework that provides provable guarantees about how much information any single individual's data contributes to a result. It works by adding carefully calibrated <em>random noise</em> to outputs (query results, model parameters, or statistics) so that the presence or absence of any individual in the dataset cannot be inferred from the output.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CONCEPT — Differential Privacy: The Intuition</span>
    <span style="background:#10b981;color:#000;padding:6px 12px;border-radius:4px;font-size:0.75rem;font-weight:600;">Technical PET</span>
  </div>
  <div style="padding:20px;">
    <p style="color:#e5e7eb;margin:0 0 16px;line-height:1.8;"><strong style="color:#a7f3d0;">The core guarantee:</strong> An algorithm is ε-differentially private if the probability of any output changes by at most a factor of e^ε when any single individual's data is added or removed from the dataset. If ε is small, an adversary gains very little information about whether any specific person was in the dataset.</p>
    <p style="color:#e5e7eb;margin:0 0 16px;line-height:1.8;"><strong style="color:#a7f3d0;">Real-world deployments:</strong> Apple uses local differential privacy to collect keyboard usage statistics and emoji frequency from iPhones without Apple's servers ever seeing individual keystrokes. Google uses DP in Chrome to understand browsing patterns. The US Census Bureau used DP in the 2020 Census to prevent re-identification from published statistics.</p>
    <p style="color:#e5e7eb;margin:0;line-height:1.8;"><strong style="color:#f59e0b;">The tradeoff:</strong> More noise (stronger privacy, lower ε) reduces the accuracy of results. Less noise (weaker privacy, higher ε) produces more accurate results. Choosing ε is a value judgment about how much privacy risk is acceptable for a given analytical benefit.</p>
  </div>
</div>

<h3>Federated Learning: Training Without Centralising Data</h3>
<p><strong>Federated Learning</strong> is a distributed machine learning approach where the model is trained on data that never leaves the devices or servers where it resides. Instead of sending raw data to a central server, each participant trains the model locally on their own data and sends only the <em>model update</em> (gradient) to the central server. The server aggregates the updates and improves the global model — without ever seeing the underlying data.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CASE STUDY — Federated Learning in Healthcare</span>
    <span style="background:#3b82f6;color:#fff;padding:6px 12px;border-radius:4px;font-size:0.75rem;font-weight:600;">Applied PET</span>
  </div>
  <div style="padding:20px;">
    <p style="color:#e5e7eb;margin:0 0 12px;line-height:1.8;">Multiple hospitals want to collaboratively train a model to detect rare tumours from medical scans. Each hospital has patient data it cannot legally share due to HIPAA and local regulations. With federated learning: each hospital trains the model on its own patient scans locally → sends only the model weights update to a coordinator → the coordinator aggregates the updates into a stronger global model → the improved model is sent back to each hospital.</p>
    <p style="color:#e5e7eb;margin:0;line-height:1.8;"><strong style="color:#10b981;">Result:</strong> A model that benefits from all hospitals' data — including rare cases that no single hospital sees frequently — without any hospital ever sharing patient records. The collaborative model consistently outperforms any single hospital's locally-trained model.</p>
  </div>
</div>

<h3>Other Key Privacy-Enhancing Technologies</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">REFERENCE — PET Toolkit for Data Scientists</span>
  </div>
  <div style="padding:20px;display:flex;flex-direction:column;gap:16px;">
    <div style="border-left:3px solid #a78bfa;padding-left:16px;">
      <strong style="color:#a78bfa;">K-Anonymity</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">A dataset satisfies k-anonymity if every record is indistinguishable from at least k-1 other records with respect to quasi-identifier attributes. Achieved by suppressing or generalising values (e.g., replacing exact age with an age range). Protects against direct re-identification but vulnerable to homogeneity and background knowledge attacks.</p>
    </div>
    <div style="border-left:3px solid #f59e0b;padding-left:16px;">
      <strong style="color:#f59e0b;">Synthetic Data Generation</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Train a generative model (GAN, VAE, or statistical model) on real sensitive data to produce artificial data that has the same statistical properties but contains no real individuals. Synthetic data can be shared freely — it is not legally personal data. Quality must be validated carefully: synthetic data that is too accurate can still leak individual information.</p>
    </div>
    <div style="border-left:3px solid #10b981;padding-left:16px;">
      <strong style="color:#10b981;">Secure Multi-Party Computation (MPC)</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Multiple parties jointly compute a function over their private inputs without any party learning the other parties' inputs. Example: two hospitals can compute the average age of all their patients combined without either hospital ever seeing the other's data. Mathematically guaranteed by cryptographic protocols.</p>
    </div>
    <div style="border-left:3px solid #ef4444;padding-left:16px;">
      <strong style="color:#ef4444;">Homomorphic Encryption</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Allows computations to be performed directly on encrypted data — without decrypting it first. The result of the computation, when decrypted, equals the result you would have gotten from computing on the plaintext data. Currently too computationally expensive for most production use cases but advancing rapidly.</p>
    </div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '18.7 Privacy-Enhancing Technologies (PETs)',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L18_7', [
                ['q' => 'What is the core mechanism by which Differential Privacy protects individuals?', 'opts' => ['By encrypting all data at rest', 'By removing names and ID numbers from the dataset', 'By adding carefully calibrated random noise to outputs so that the presence or absence of any single individual cannot be inferred', 'By restricting query access to authorised users only'], 'ans' => 2, 'exp' => 'Differential Privacy adds mathematically calibrated noise to results (statistics, model outputs, published data) so that an adversary cannot determine whether any specific individual was in the dataset. The privacy guarantee is formal and mathematical — not just practical obscurity.'],
                ['q' => 'In Federated Learning, what is sent from each participant to the central server?', 'opts' => ['The raw training data in encrypted form', 'Only the model weights update (gradient) — never the underlying data', 'A summary statistics report of the data', 'The fully trained local model for direct deployment'], 'ans' => 1, 'exp' => 'In federated learning, each participant trains the model locally on their own data and sends only the model update (gradient/weight change) to the central server. The raw data never leaves the local device or institution. The server aggregates these updates to improve the global model.'],
                ['q' => 'What is K-Anonymity\'s primary protection mechanism?', 'opts' => ['Encrypting quasi-identifier fields with AES-256', 'Ensuring every record is indistinguishable from at least k-1 other records on quasi-identifier attributes, through generalisation or suppression', 'Adding noise to numerical fields to prevent exact matching', 'Removing all quasi-identifiers from the dataset before release'], 'ans' => 1, 'exp' => 'K-Anonymity ensures that for any combination of quasi-identifier values, at least k records share the same values — making it impossible to single out a specific individual. It is achieved by generalising precise values (exact age → age range) or suppressing outlier records.'],
                ['q' => 'What is the primary privacy risk associated with Synthetic Data Generation?', 'opts' => ['Synthetic data always performs worse on ML tasks than real data', 'Synthetic data that too closely mimics the original can still leak information about individuals in the training set — particularly for rare or extreme cases', 'Synthetic data cannot be used for model training', 'Regulators do not permit synthetic data for research purposes'], 'ans' => 1, 'exp' => 'High-quality synthetic data that closely replicates the original distribution can "memorise" rare individuals from the training set — particularly outliers. A synthetic dataset might generate a record nearly identical to a real person\'s unique combination of attributes. Membership inference attacks can exploit this.'],
                ['q' => 'Apple uses Differential Privacy for keyboard usage statistics. What does this mean in practice?', 'opts' => ['Apple encrypts all keystrokes and stores them on its servers in encrypted form', 'Apple can collect aggregate statistics about typing patterns without Apple\'s servers ever seeing individual users\' keystrokes', 'Apple deletes all keyboard data after 24 hours', 'Apple only collects keyboard data from users who explicitly opt in'], 'ans' => 1, 'exp' => 'Apple\'s local differential privacy implementation adds noise to each user\'s data on-device before it is sent. The noise is designed so that: (a) the aggregate across millions of users still produces accurate population-level statistics, and (b) Apple\'s servers cannot determine any individual user\'s actual keystrokes.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 18.8 — Data Governance: Frameworks, Roles & Accountability
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Data Governance: Frameworks, Roles & Accountability</h2>
<p><strong>Data governance</strong> is the system of policies, processes, roles, and standards that determines how an organisation manages its data assets across their entire lifecycle — from collection and storage through use, sharing, and deletion. Without governance, data science operates in chaos: different teams use inconsistent definitions, nobody knows who is responsible when data is misused, models are trained on datasets of unknown provenance, and when a regulator asks "show me your data lineage," the answer is a shrug.</p>

<p>Governance is not bureaucracy for its own sake — it is the organisational infrastructure that makes responsible, trustworthy, and legally compliant data science possible at scale. The organisations that build the most impactful data products are almost always the ones that take governance seriously.</p>

<h3>The Data Governance Framework: Key Components</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — Core Data Governance Components</span>
  </div>
  <div style="padding:20px;display:flex;flex-direction:column;gap:16px;">
    <div style="border-left:3px solid #10b981;padding-left:16px;">
      <strong style="color:#10b981;">Data Catalogue & Metadata Management</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">A centralised inventory of all data assets with documented metadata: what the data is, where it came from, how it was collected, what transformations it has undergone, who owns it, and what it can be used for. Without a catalogue, data scientists waste enormous time discovering data and organisations lose track of what sensitive information they hold.</p>
    </div>
    <div style="border-left:3px solid #3b82f6;padding-left:16px;">
      <strong style="color:#3b82f6;">Data Lineage</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">The documented history of a piece of data — where it originated, every transformation it has undergone, every system it has passed through, and every model or report it has been used to build. Data lineage is critical for debugging data quality issues, understanding model inputs, and responding to regulatory audit requests.</p>
    </div>
    <div style="border-left:3px solid #f59e0b;padding-left:16px;">
      <strong style="color:#f59e0b;">Data Classification & Handling Standards</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">A tiered system that assigns data to sensitivity levels (e.g., Public, Internal, Confidential, Restricted) and specifies the access controls, encryption requirements, retention periods, and permissible uses for each level. Special category personal data sits at the highest tier.</p>
    </div>
    <div style="border-left:3px solid #a78bfa;padding-left:16px;">
      <strong style="color:#a78bfa;">Access Control & Data Minimisation Policies</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">The principle of least privilege: every person and system should have access only to the data they strictly need for their defined role. Access should be role-based (RBAC), time-limited where appropriate, and logged for audit purposes. Data minimisation applies to internal use as much as external sharing.</p>
    </div>
    <div style="border-left:3px solid #ef4444;padding-left:16px;">
      <strong style="color:#ef4444;">Retention & Deletion Policies</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Documented schedules specifying how long each category of data is retained and the technical processes for secure deletion. GDPR's Storage Limitation principle and CCPA's right to delete both require operational deletion processes. Data that is not deleted creates ongoing privacy risk for no ongoing business benefit.</p>
    </div>
  </div>
</div>

<h3>Key Roles in Data Governance</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">REFERENCE — Data Governance Roles & Responsibilities</span>
  </div>
  <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
    <div style="display:grid;grid-template-columns:200px 1fr;gap:16px;align-items:start;">
      <strong style="color:#10b981;">Data Controller</strong>
      <span style="color:#e5e7eb;line-height:1.7;">The entity that determines the <em>purposes</em> and <em>means</em> of processing personal data. Bears primary legal responsibility under GDPR. Typically the business or organisation collecting the data.</span>
    </div>
    <div style="border-top:1px solid var(--border);padding-top:14px;display:grid;grid-template-columns:200px 1fr;gap:16px;align-items:start;">
      <strong style="color:#3b82f6;">Data Processor</strong>
      <span style="color:#e5e7eb;line-height:1.7;">A third party that processes personal data on behalf of and under instruction from the controller. Cloud providers, analytics tools, and payroll services are typically processors. Must have a Data Processing Agreement (DPA) with the controller.</span>
    </div>
    <div style="border-top:1px solid var(--border);padding-top:14px;display:grid;grid-template-columns:200px 1fr;gap:16px;align-items:start;">
      <strong style="color:#f59e0b;">Data Protection Officer (DPO)</strong>
      <span style="color:#e5e7eb;line-height:1.7;">A senior independent expert responsible for advising on GDPR compliance, conducting Data Protection Impact Assessments, and serving as the primary contact for regulators. Mandatory under GDPR for public authorities and organisations processing special category data at scale.</span>
    </div>
    <div style="border-top:1px solid var(--border);padding-top:14px;display:grid;grid-template-columns:200px 1fr;gap:16px;align-items:start;">
      <strong style="color:#a78bfa;">Data Owner</strong>
      <span style="color:#e5e7eb;line-height:1.7;">A senior business stakeholder accountable for a specific dataset — its quality, classification, and appropriate use. Data owners approve access requests and are responsible for ensuring the dataset complies with governance policies.</span>
    </div>
    <div style="border-top:1px solid var(--border);padding-top:14px;display:grid;grid-template-columns:200px 1fr;gap:16px;align-items:start;">
      <strong style="color:#ef4444;">Data Steward</strong>
      <span style="color:#e5e7eb;line-height:1.7;">A technical subject-matter expert responsible for day-to-day management of a dataset: maintaining documentation, managing metadata, resolving data quality issues, and enforcing governance standards in practice.</span>
    </div>
  </div>
</div>

<h3>Data Protection Impact Assessments (DPIAs)</h3>
<p>A <strong>Data Protection Impact Assessment (DPIA)</strong> is a mandatory process under GDPR Article 35 for processing activities that are "likely to result in a high risk" to individuals' rights — including large-scale processing of special category data, systematic profiling with significant effects, and large-scale surveillance using new technologies. A DPIA must be completed <em>before</em> the processing begins, not after. It documents the processing's purpose, necessity, risks, and the technical and organisational measures taken to mitigate them. If a residual high risk remains after mitigation, the supervisory authority must be consulted before proceeding.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '18.8 Data Governance: Frameworks, Roles & Accountability',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L18_8', [
                ['q' => 'What is the difference between a Data Controller and a Data Processor under GDPR?', 'opts' => ['A controller stores data; a processor analyses it', 'A controller determines the purposes and means of processing and bears primary legal responsibility; a processor handles data on the controller\'s behalf under instruction', 'A controller is a government entity; a processor is a private company', 'A controller owns the data; a processor rents access to it'], 'ans' => 1, 'exp' => 'The Data Controller decides why and how data is processed and carries primary GDPR accountability. The Data Processor acts on the controller\'s instructions — cloud infrastructure providers, payroll services, and analytics vendors are typically processors. Both must have a Data Processing Agreement in place.'],
                ['q' => 'What is Data Lineage and why is it important for data science?', 'opts' => ['The legal chain of ownership of a dataset', 'The documented history of a data asset — its origin, every transformation it has undergone, and every system and model it has been used in', 'The list of people who have accessed a dataset', 'The version history of a dataset in a Git repository'], 'ans' => 1, 'exp' => 'Data lineage traces the full journey of data from source to every downstream use. It is critical for debugging data quality issues (knowing exactly where a problem was introduced), regulatory compliance (demonstrating data provenance to auditors), and model governance (understanding what data trained a deployed model).'],
                ['q' => 'What is the principle of least privilege in data access control?', 'opts' => ['Only the most senior staff should have access to sensitive data', 'Every person and system should have access only to the specific data they strictly need for their defined role — no more', 'Public data should be freely accessible to all employees', 'Data access privileges should be reviewed every quarter'], 'ans' => 1, 'exp' => 'The principle of least privilege (PoLP) minimises the blast radius of insider threats, credential theft, and accidental misuse by limiting each actor\'s access to only what their specific role requires. It is a foundational security and governance principle implemented through Role-Based Access Control (RBAC).'],
                ['q' => 'When must a Data Protection Impact Assessment (DPIA) be completed under GDPR?', 'opts' => ['Within 30 days after a new data processing activity launches', 'Before the processing begins — when it is likely to result in high risk to individuals\' rights', 'Only after a data breach has occurred', 'Annually for all data processing activities, regardless of risk'], 'ans' => 1, 'exp' => 'GDPR Article 35 requires a DPIA to be conducted BEFORE beginning any processing likely to result in high risk — particularly large-scale special category data processing, systematic profiling, or surveillance using new technologies. Conducting it after the fact defeats the purpose of the risk assessment.'],
                ['q' => 'What is the role of a Data Protection Officer (DPO) in an organisation?', 'opts' => ['To approve all data access requests from employees', 'To write all privacy policies and terms of service', 'To independently advise on GDPR compliance, conduct DPIAs, and serve as the primary contact for data protection authorities', 'To manage the organisation\'s cybersecurity infrastructure'], 'ans' => 2, 'exp' => 'The DPO is an independent expert — they must be able to perform their duties without instruction from the employer. They advise the organisation on compliance, monitor compliance, conduct DPIAs, and act as the contact point for supervisory authorities and data subjects. Their independence is protected by GDPR Article 38.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 18.9 — Responsible AI: Transparency, Explainability & Accountability
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Responsible AI: Transparency, Explainability & Accountability</h2>
<p>A machine learning model that produces accurate predictions is not necessarily a trustworthy one. Trustworthiness requires more than accuracy: it requires that you can understand <em>why</em> a model makes the decisions it does, that you can explain those decisions to affected people in terms they can act on, that someone is accountable when the model causes harm, and that the model's limitations are communicated honestly to everyone who relies on it. These are the demands of <strong>Responsible AI</strong> — an emerging discipline that sits at the intersection of technical design, organisational governance, and public accountability.</p>

<h3>Transparency vs. Explainability: An Important Distinction</h3>
<p>These terms are often used interchangeably, but they describe different things:</p>
<ul style="line-height:2.2;color:#e5e7eb;">
  <li><strong>Transparency</strong> is about process — disclosing that an automated system was used, what data it was trained on, how it was evaluated, and what its known limitations are. Transparency does not require revealing proprietary algorithms or training data — but it does require honest disclosure of material facts about the system.</li>
  <li><strong>Explainability</strong> is about output — the ability to articulate, for a specific decision about a specific person, why the system produced that result. "Your loan was denied because your credit utilisation ratio of 87% exceeded our threshold of 80%" is an explanation. "The model said no" is not.</li>
</ul>

<h3>The GDPR Right to Explanation (Article 22)</h3>
<p>Under GDPR Article 22, when an automated system makes a decision that "significantly affects" a person — loan applications, job screening, insurance pricing, medical triage — the affected person has the right to: (a) be told that an automated system was used; (b) receive meaningful information about the logic involved; and (c) have the decision reviewed by a human being. This is not a right to a full technical explanation of the model — but it is a right to enough information to understand and challenge the decision.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CASE STUDY — UK Home Office Visa Algorithm (2020)</span>
    <span style="background:#f59e0b;color:#000;padding:6px 12px;border-radius:4px;font-size:0.75rem;font-weight:600;">Accountability Failure</span>
  </div>
  <div style="padding:20px;">
    <p style="color:#e5e7eb;margin:0 0 12px;line-height:1.8;">The UK Home Office used an automated system to stream visa applications into fast-track, standard, or slow-track processing queues. The algorithm used nationality as a primary input — meaning applicants from certain countries were systematically routed to slow-track processing and faced dramatically higher refusal rates. The system had been operating for years before a legal challenge forced disclosure.</p>
    <p style="color:#e5e7eb;margin:0 0 12px;line-height:1.8;"><strong style="color:#ef4444;">The problem:</strong> Applicants were not told an algorithm was being used. They were not told nationality was a factor. They could not appeal the queue assignment. There was no meaningful human review of the algorithmic routing. All of this violated both Article 22 principles and fundamental human rights law.</p>
    <p style="color:#e5e7eb;margin:0;line-height:1.8;"><strong style="color:#10b981;">Outcome:</strong> Following legal challenge by Foxglove Legal and the Joint Council for the Welfare of Immigrants, the Home Office was forced to scrap the system entirely in 2020. The case established that automated immigration decision-support systems must be transparent, auditable, and subject to meaningful human review.</p>
  </div>
</div>

<h3>Explainability Techniques for ML Models</h3>
<p>In practice, many of the highest-performing ML models — deep neural networks, gradient boosted ensembles with hundreds of trees — are inherently difficult to explain at the level of individual predictions. A growing toolkit of post-hoc explainability methods has been developed to address this:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">REFERENCE — Explainability Methods for Black-Box Models</span>
  </div>
  <div style="padding:20px;display:flex;flex-direction:column;gap:16px;">
    <div style="border-left:3px solid #10b981;padding-left:16px;">
      <strong style="color:#10b981;">SHAP (SHapley Additive exPlanations)</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Based on cooperative game theory, SHAP assigns each feature a contribution value for a specific prediction — telling you exactly how much each input pushed the prediction up or down. Provides both local (single prediction) and global (overall model) explanations. Widely regarded as the current gold standard for model explainability.</p>
    </div>
    <div style="border-left:3px solid #3b82f6;padding-left:16px;">
      <strong style="color:#3b82f6;">LIME (Local Interpretable Model-Agnostic Explanations)</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Explains individual predictions by training a simple interpretable model (e.g., linear regression) that approximates the complex model's behaviour in the local neighbourhood of the input being explained. Model-agnostic — works with any model. Less mathematically rigorous than SHAP but faster for some use cases.</p>
    </div>
    <div style="border-left:3px solid #f59e0b;padding-left:16px;">
      <strong style="color:#f59e0b;">Counterfactual Explanations</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Answers the question: "What is the minimum change to the input that would have flipped the decision?" Example: "If your annual income were ₱50,000 higher and your credit utilisation were below 75%, your loan application would have been approved." Counterfactuals are often the most actionable explanations for affected individuals because they specify what to change.</p>
    </div>
    <div style="border-left:3px solid #a78bfa;padding-left:16px;">
      <strong style="color:#a78bfa;">Intrinsically Interpretable Models</strong>
      <p style="color:#e5e7eb;margin:6px 0 0;line-height:1.7;">Sometimes the most responsible choice is not to use a black-box model at all. Linear regression, logistic regression, shallow decision trees, and scorecards are intrinsically interpretable — the explanation is the model itself. In high-stakes domains like criminal justice, medical diagnosis, and credit scoring, regulators and courts increasingly prefer interpretable models over "explain after the fact" approaches.</p>
    </div>
  </div>
</div>

<h3>Accountability Structures: Model Cards & Datasheets</h3>
<p>Two documentation practices have emerged as standards for model transparency. <strong>Model Cards</strong> (proposed by Mitchell et al. at Google, 2019) are short documents that accompany deployed ML models, specifying: intended use cases, out-of-scope uses, performance metrics disaggregated by demographic group, known biases, and recommended mitigation. <strong>Datasheets for Datasets</strong> (proposed by Gebru et al., 2018) document the motivation, composition, collection process, labelling process, and recommended uses for a dataset. Together, these documents create an auditable record of what a model and its training data were designed for, how they perform, and where they should not be used.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '18.9 Responsible AI: Transparency, Explainability & Accountability',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L18_9', [
                ['q' => 'What is the difference between Transparency and Explainability in AI systems?', 'opts' => ['They are synonyms — both mean publishing the model\'s source code', 'Transparency is about disclosing that and how a system operates (process); Explainability is about articulating why a specific decision was made for a specific person (output)', 'Transparency is a legal requirement; Explainability is voluntary', 'Transparency applies to data collection; Explainability applies to model training'], 'ans' => 1, 'exp' => 'Transparency addresses process-level disclosure: that an AI system was used, what it was trained on, what its limitations are. Explainability addresses outcome-level justification: for a specific decision about a specific person, why did the system produce that result? Both are required for truly responsible AI, but they are not the same thing.'],
                ['q' => 'What does GDPR Article 22 entitle a person to when an automated system significantly affects them?', 'opts' => ['A full technical explanation of the model\'s architecture and weights', 'The right to be told a system was used, receive meaningful information about its logic, and have the decision reviewed by a human', 'The right to access all data the model was trained on', 'The right to have the model retrained without their data'], 'ans' => 1, 'exp' => 'Article 22 grants three rights: (1) to be informed an automated system was used, (2) to receive meaningful information about the logic involved — not a full technical dump, but enough to understand the decision, and (3) to have a human being review the automated decision. The right to human review is particularly significant for high-stakes applications.'],
                ['q' => 'What makes Counterfactual Explanations particularly useful for people affected by automated decisions?', 'opts' => ['They provide the global feature importance ranking for the model', 'They specify the minimum changes to the input that would have flipped the decision — giving actionable guidance on what to change', 'They visualise the model\'s decision boundary', 'They explain the model\'s average behaviour across the training set'], 'ans' => 1, 'exp' => 'Counterfactual explanations answer "what would have had to be different for you to receive a different outcome?" — for example, "if your credit utilisation were below 75% you would have been approved." This is actionable: the person knows what to do. Feature importance scores tell you what the model cares about globally, but counterfactuals tell you what to change in your specific situation.'],
                ['q' => 'What was the fundamental transparency failure in the UK Home Office visa algorithm case?', 'opts' => ['The algorithm was using an outdated machine learning model', 'Applicants were not told an algorithm was being used, nationality was a hidden factor, there was no appeal mechanism, and no meaningful human review existed', 'The system used personal data without consent', 'The algorithm was not accurate enough and misclassified too many applicants'], 'ans' => 1, 'exp' => 'The case exemplifies a transparency and accountability failure: automated processing affecting people\'s immigration rights with no disclosure to applicants, no explanation of the algorithm\'s logic, no audit trail, and no effective human review mechanism. It violated both GDPR Article 22 principles and human rights law, resulting in the system being scrapped.'],
                ['q' => 'What is the primary purpose of a Model Card for a deployed ML model?', 'opts' => ['To protect the model\'s intellectual property through documentation', 'To document intended uses, out-of-scope uses, performance disaggregated by demographic group, known biases, and limitations — creating an auditable accountability record', 'To replace the need for a Data Protection Impact Assessment', 'To serve as marketing material for the AI product'], 'ans' => 1, 'exp' => 'Model Cards (Mitchell et al., 2019) are standardised documentation accompanying deployed models. They record what the model was designed for, what it should NOT be used for, how it performs across demographic subgroups, known biases, and recommended mitigation measures. This creates the accountability trail needed for auditing, regulatory compliance, and responsible deployment.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 18.10 — Building an Ethical Data Science Practice
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Building an Ethical Data Science Practice</h2>
<p>The preceding nine lessons have given you the vocabulary, frameworks, and case studies to understand what ethical data science means in theory. This final lesson addresses the practical challenge: <strong>how do you actually build these principles into your day-to-day work?</strong> Ethical data science is not a project you complete once — it is a set of habits, processes, and professional commitments that must be integrated into every phase of how you work with data.</p>

<h3>The Ethical ML Checklist: Questions for Every Project Stage</h3>
<p>Before and during any data science project that affects people, systematically ask the following questions. A question you cannot answer is a risk you are carrying silently.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CHECKLIST — Ethical Questions by Pipeline Stage</span>
  </div>
  <div style="padding:20px;display:flex;flex-direction:column;gap:20px;">
    <div>
      <p style="color:#10b981;font-weight:700;margin:0 0 10px;text-transform:uppercase;font-size:0.75rem;letter-spacing:0.05em;">Problem Framing</p>
      <ul style="color:#e5e7eb;margin:0;padding-left:20px;line-height:2.1;">
        <li>Who benefits from this system? Who bears the risks?</li>
        <li>Is this the right problem to solve? Is automation the right solution?</li>
        <li>Have the communities affected by this system been consulted in its design?</li>
        <li>What happens to people who are incorrectly classified or rejected?</li>
      </ul>
    </div>
    <div style="border-top:1px solid var(--border);padding-top:20px;">
      <p style="color:#3b82f6;font-weight:700;margin:0 0 10px;text-transform:uppercase;font-size:0.75rem;letter-spacing:0.05em;">Data Collection & Preparation</p>
      <ul style="color:#e5e7eb;margin:0;padding-left:20px;line-height:2.1;">
        <li>What is the legal basis for using this data? Is it documented?</li>
        <li>Does the training data adequately represent all groups the model will affect?</li>
        <li>Are any features proxies for protected attributes (e.g., zip code correlating with race)?</li>
        <li>Is the labelling process consistent and free of annotator bias?</li>
      </ul>
    </div>
    <div style="border-top:1px solid var(--border);padding-top:20px;">
      <p style="color:#f59e0b;font-weight:700;margin:0 0 10px;text-transform:uppercase;font-size:0.75rem;letter-spacing:0.05em;">Model Training & Evaluation</p>
      <ul style="color:#e5e7eb;margin:0;padding-left:20px;line-height:2.1;">
        <li>Have you evaluated performance disaggregated by protected group — not just overall accuracy?</li>
        <li>Which fairness criterion is most appropriate for this use case? Is that choice documented?</li>
        <li>Have you tested for adversarial inputs or edge cases that could cause systematic failure?</li>
        <li>Is a more interpretable model achievable at acceptable accuracy cost?</li>
      </ul>
    </div>
    <div style="border-top:1px solid var(--border);padding-top:20px;">
      <p style="color:#a78bfa;font-weight:700;margin:0 0 10px;text-transform:uppercase;font-size:0.75rem;letter-spacing:0.05em;">Deployment & Monitoring</p>
      <ul style="color:#e5e7eb;margin:0;padding-left:20px;line-height:2.1;">
        <li>Can affected individuals understand why a decision was made? Can they appeal it?</li>
        <li>Is there a human review process for high-stakes or borderline decisions?</li>
        <li>Are you monitoring for performance degradation and fairness drift over time?</li>
        <li>Is there a clear process for decommissioning the model if it causes harm?</li>
      </ul>
    </div>
  </div>
</div>

<h3>Privacy by Design: The Foundational Principle</h3>
<p><strong>Privacy by Design (PbD)</strong>, developed by Ann Cavoukian, is a framework that holds privacy should be embedded into the architecture of systems from the very beginning — not added as an afterthought or bolted on after deployment. It has seven foundational principles and is now a legal requirement under GDPR Article 25 ("Data Protection by Design and by Default").</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — Privacy by Design: Seven Principles</span>
  </div>
  <div style="padding:20px;display:flex;flex-direction:column;gap:12px;">
    <div style="display:flex;gap:12px;align-items:flex-start;">
      <span style="background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:'JetBrains Mono',monospace;white-space:nowrap;flex-shrink:0;">1</span>
      <span style="color:#e5e7eb;line-height:1.7;"><strong>Proactive, not Reactive</strong> — Anticipate and prevent privacy problems before they occur. Do not wait for breaches to happen.</span>
    </div>
    <div style="display:flex;gap:12px;align-items:flex-start;">
      <span style="background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:'JetBrains Mono',monospace;white-space:nowrap;flex-shrink:0;">2</span>
      <span style="color:#e5e7eb;line-height:1.7;"><strong>Privacy as the Default</strong> — The maximum privacy protection should be the default. Users should not have to take action to protect themselves — they should have to actively opt in to less privacy.</span>
    </div>
    <div style="display:flex;gap:12px;align-items:flex-start;">
      <span style="background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:'JetBrains Mono',monospace;white-space:nowrap;flex-shrink:0;">3</span>
      <span style="color:#e5e7eb;line-height:1.7;"><strong>Privacy Embedded into Design</strong> — Privacy is a core component of the system, not a feature layer added on top of an otherwise privacy-indifferent architecture.</span>
    </div>
    <div style="display:flex;gap:12px;align-items:flex-start;">
      <span style="background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:'JetBrains Mono',monospace;white-space:nowrap;flex-shrink:0;">4</span>
      <span style="color:#e5e7eb;line-height:1.7;"><strong>Full Functionality — Positive-Sum</strong> — Privacy and functionality are not zero-sum. It should be possible to achieve both fully. "Privacy vs. utility" is a false dilemma in well-designed systems.</span>
    </div>
    <div style="display:flex;gap:12px;align-items:flex-start;">
      <span style="background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:'JetBrains Mono',monospace;white-space:nowrap;flex-shrink:0;">5</span>
      <span style="color:#e5e7eb;line-height:1.7;"><strong>End-to-End Security</strong> — Strong security protections throughout the entire data lifecycle — from collection through deletion.</span>
    </div>
    <div style="display:flex;gap:12px;align-items:flex-start;">
      <span style="background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:'JetBrains Mono',monospace;white-space:nowrap;flex-shrink:0;">6</span>
      <span style="color:#e5e7eb;line-height:1.7;"><strong>Visibility & Transparency</strong> — All practices should be visible and verifiable to users and regulators. No hidden agendas, no secret data flows.</span>
    </div>
    <div style="display:flex;gap:12px;align-items:flex-start;">
      <span style="background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:'JetBrains Mono',monospace;white-space:nowrap;flex-shrink:0;">7</span>
      <span style="color:#e5e7eb;line-height:1.7;"><strong>Respect for User Privacy</strong> — User-centric: design systems that protect the interests of individual users above organisational convenience.</span>
    </div>
  </div>
</div>

<h3>When to Raise Concerns: Professional Courage in Data Science</h3>
<p>Ethical frameworks and technical tools are only effective if you are willing to use them — and to speak up when something is wrong. Data scientists regularly face pressure to deliver results faster, to use data in ways that were not consented to, to skip fairness evaluation, or to deploy models with known limitations in high-stakes contexts. The professional response is not to comply silently.</p>

<p>Raising ethics concerns requires professional courage: documenting your objections in writing, escalating through appropriate channels, involving legal or compliance teams proactively, and in extreme cases, understanding whistleblower protections available in your jurisdiction. Many of the most significant data ethics failures in history were known internally before they caused public harm — and the people who knew and said nothing bear moral responsibility alongside those who designed the systems.</p>

<p>Ethical data science is ultimately a personal as well as an institutional commitment. It means building systems you would be proud to have publicly scrutinised — not just systems that pass a compliance audit. It means asking who benefits and who is harmed, not just whether the model is accurate. And it means recognising that the privilege of working with powerful data tools comes with a proportional responsibility to use them well.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '18.10 Building an Ethical Data Science Practice',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L18_10', [
                ['q' => 'During the Problem Framing stage, what is the most important ethical question to ask about the communities affected by a system?', 'opts' => ['What is the projected accuracy of the model on their data?', 'Have the communities affected by this system been consulted in its design?', 'What volume of data is available about this community?', 'Does the community have internet access to use the system?'], 'ans' => 1, 'exp' => 'Consulting affected communities during problem framing — not just after deployment — is fundamental to ethical design. Communities understand their own context, risks, and needs better than outside designers. Systems built without this consultation regularly fail in ways that were foreseeable and could have been prevented.'],
                ['q' => 'What does "Privacy as the Default" mean in the Privacy by Design framework?', 'opts' => ['All data should be encrypted at rest by default', 'Maximum privacy protection should be the system default — users should not have to take action to protect themselves; they should have to actively opt in to less privacy', 'The default data retention period should be 30 days', 'Privacy settings should be visible on the main page of any application'], 'ans' => 1, 'exp' => 'Privacy as the Default (PbD Principle 2) means the system automatically protects privacy at its most conservative setting. Users should not need to hunt through settings menus to protect themselves — the most privacy-protective configuration should be what they get without any action. Opt-in for data sharing, not opt-out.'],
                ['q' => 'Why is disaggregated model evaluation (evaluating separately for demographic subgroups) essential — even if overall accuracy is high?', 'opts' => ['Regulators require it for all deployed models', 'A model with high overall accuracy can still perform significantly worse for specific subgroups — this gap would be invisible in aggregate metrics alone', 'It allows you to set different accuracy thresholds for different groups', 'Disaggregated evaluation always produces higher accuracy scores'], 'ans' => 1, 'exp' => 'Aggregate accuracy is a misleading metric for fairness. A model that is 95% accurate overall might be 60% accurate for a minority subgroup — but the subgroup\'s poor performance is hidden in the average. Disaggregated evaluation disaggregates accuracy, false positive rate, false negative rate, and other metrics separately for each protected group, making subgroup disparities visible.'],
                ['q' => 'A data scientist discovers that a deployed loan model significantly disadvantages applicants from a particular ethnic group. Their manager says "the numbers look fine overall, don\'t worry about it." What is the ethically appropriate response?', 'opts' => ['Accept the manager\'s assessment and move on — overall accuracy is what matters', 'Document the finding in writing, escalate through appropriate channels (including legal/compliance), and if necessary invoke whistleblower protections — silent compliance makes you morally responsible for the ongoing harm', 'Quietly adjust the model threshold on their own without telling anyone', 'Wait until there is a public complaint before acting'], 'ans' => 1, 'exp' => 'When a data scientist identifies a fairness or privacy harm, the ethical professional responsibility is to raise it — in writing, through appropriate escalation channels, and with legal/compliance teams if necessary. Silent compliance with a known harm does not insulate you from moral responsibility. Many of history\'s largest data ethics failures were known internally before causing public harm.'],
                ['q' => 'What makes Privacy by Design a legal requirement rather than just a best practice recommendation?', 'opts' => ['ISO 27001 mandates it for all certified organisations', 'GDPR Article 25 requires "Data Protection by Design and by Default" for all organisations processing personal data of EU residents', 'All countries have adopted the Privacy by Design framework into national law', 'It becomes legally required only after an organisation has experienced a data breach'], 'ans' => 1, 'exp' => 'GDPR Article 25 ("Data Protection by Design and by Default") codified Privacy by Design into EU law. It requires data controllers to implement appropriate technical and organisational measures designed to implement data protection principles and to integrate the necessary safeguards into the processing system itself — not added on after the fact.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 18.11 — Final Exam (Org-Locked)
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            // 18.1 — Why ethics matters
            ['q' => 'What is the "scale problem" that makes data science ethics particularly significant compared to individual human decision-making?', 'opts' => ['Datasets are too large for manual review', 'Automated systems apply the same biased assumptions to millions of people simultaneously — amplifying harm beyond what any individual could cause', 'Privacy laws are written for individual actors, not systems', 'Scale requires more computing power, reducing accuracy'], 'ans' => 1, 'exp' => 'The scale problem is that an algorithm makes the same flawed decision for millions of people at once. A biased human hiring manager may affect hundreds of candidates over a career. A biased hiring algorithm affects millions of applications in a single year, consistently applying the same discriminatory logic at massive scale.'],
            ['q' => 'Which ethical principle requires data scientists to proactively anticipate ways their model could cause harm before deploying it?', 'opts' => ['Beneficence', 'Autonomy', 'Non-Maleficence', 'Justice'], 'ans' => 2, 'exp' => 'Non-Maleficence — first, do no harm — is a preventive principle. It requires actively anticipating harm, misuse, and discriminatory outcomes before they occur, and designing to prevent them. It is not about responding to harm after it happens.'],
            // 18.2 — Personal data
            ['q' => 'Under GDPR, which of the following is NOT personal data?', 'opts' => ['A unique device fingerprint linked to browsing history', 'A combination of zip code, birth date and gender that can re-identify an individual', 'A dataset that has been genuinely and irreversibly anonymised such that no individual can be identified even through combination', 'A person\'s IP address'], 'ans' => 2, 'exp' => 'Only truly anonymised data — where the link to any real individual has been permanently and irreversibly severed — falls outside GDPR\'s definition of personal data. Device fingerprints, quasi-identifier combinations capable of re-identification, and IP addresses are all personal data under GDPR.'],
            ['q' => 'Health data, genetic data, and data about a person\'s sexual orientation are examples of which GDPR category?', 'opts' => ['Standard personal data requiring basic protection', 'Special Category data under Article 9, requiring explicit consent and stronger legal basis', 'Pseudonymised data subject to reduced obligations', 'Administrative data exempt from GDPR under the household exemption'], 'ans' => 1, 'exp' => 'GDPR Article 9 designates these as "special categories" of sensitive data that carry elevated risks of discrimination and harm. Processing them requires either explicit consent or another specific Article 9(2) legal basis, and generally stricter safeguards than standard personal data.'],
            // 18.3 — GDPR consent & legal basis
            ['q' => 'A company\'s privacy policy states: "By continuing to use our website, you agree to us selling your data to advertising partners." Is this valid GDPR consent?', 'opts' => ['Yes — continued use constitutes affirmative action', 'No — GDPR consent must be freely given, specific, informed, and an unambiguous affirmative action; implied consent through continued use is not sufficient', 'Yes — it is disclosed in the privacy policy which users can read', 'Yes — only for non-sensitive data categories'], 'ans' => 1, 'exp' => 'GDPR requires consent to be an unambiguous affirmative action — not implied by continued use, not buried in T&Cs, and not presumed from silence. Continuing to use a website is not a clear affirmative act of consent to data selling. This practice violates GDPR\'s consent requirements.'],
            ['q' => 'Which GDPR principle requires that the legal basis for data processing must be documented and demonstrable?', 'opts' => ['Purpose Limitation', 'Data Minimisation', 'Accountability', 'Accuracy'], 'ans' => 2, 'exp' => 'The Accountability principle (Article 5(2)) requires that the data controller must not only comply with GDPR\'s principles but be able to demonstrate that compliance. This means documenting the legal basis for each processing activity, maintaining records, conducting DPIAs, and having evidence of all governance measures.'],
            // 18.4 — Global laws
            ['q' => 'Which US privacy law specifically gives California consumers the right to opt out of the "sale" of their personal data — including sharing with advertising partners?', 'opts' => ['HIPAA', 'FERPA', 'CCPA/CPRA', 'FTC Act Section 5'], 'ans' => 2, 'exp' => 'The California Consumer Privacy Act (CCPA), significantly strengthened by CPRA, gives California residents the right to opt out of the sale or sharing of their personal data for cross-context behavioural advertising. The broad definition of "sale" includes sharing with ad networks even without monetary exchange.'],
            // 18.5 — Algorithmic bias
            ['q' => 'Why is using "zip code" as a feature in a credit scoring model potentially an example of proxy discrimination?', 'opts' => ['Zip codes are too imprecise to be useful predictors', 'Zip codes are not legally considered personal data', 'Zip codes strongly correlate with race in many countries due to residential segregation — making them a proxy for a protected attribute even though race is not explicitly used', 'Using location data requires special consent under CCPA'], 'ans' => 2, 'exp' => 'Due to historical residential segregation patterns in many countries, zip code strongly correlates with racial composition. Using it as a feature in a credit model means the model is effectively using race as a factor through a proxy — even though race is never explicitly in the feature set. This is proxy discrimination.'],
            // 18.6 — Fairness mitigation
            ['q' => 'Which bias mitigation stage involves adding fairness constraints directly to the model\'s loss function during training?', 'opts' => ['Pre-processing', 'In-processing', 'Post-processing', 'Data augmentation'], 'ans' => 1, 'exp' => 'In-processing mitigation modifies the training objective itself — penalising the model when it produces unfair disparities across groups during learning. This requires access to the training process. Pre-processing modifies the data before training; post-processing adjusts outputs after prediction.'],
            // 18.7 — PETs
            ['q' => 'In Federated Learning, which element is sent from participant devices to the central server?', 'opts' => ['Encrypted copies of the raw training data', 'Only the model weight update (gradient) — the raw data never leaves the local device', 'A random sample of 10% of the training records', 'The predicted labels for the training set'], 'ans' => 1, 'exp' => 'Federated learning achieves privacy by keeping raw data local. Each participant trains on their own data and shares only the mathematical update (gradient/weight delta) with the central server. The server aggregates these updates into an improved global model without ever accessing the underlying training records.'],
            // 18.8 — Data governance
            ['q' => 'What distinguishes a Data Controller from a Data Processor under GDPR?', 'opts' => ['A controller encrypts data; a processor stores it', 'A controller determines purposes and means of processing and bears primary legal responsibility; a processor handles data on the controller\'s behalf under instruction', 'A controller is always a public body; a processor is always a private company', 'A controller manages data access; a processor manages data quality'], 'ans' => 1, 'exp' => 'The Controller-Processor distinction is fundamental to GDPR accountability. The controller decides why and how data is processed and bears primary legal liability. The processor acts on the controller\'s instructions under a Data Processing Agreement. Both have separate legal obligations under GDPR.'],
            // 18.9 — Responsible AI
            ['q' => 'What is the purpose of a Model Card for a deployed ML model?', 'opts' => ['To patent the model\'s architecture', 'To document intended uses, out-of-scope uses, disaggregated performance metrics, known biases, and limitations — creating an accountability record', 'To encrypt the model\'s weights against theft', 'To certify the model has passed all regulatory requirements'], 'ans' => 1, 'exp' => 'Model Cards (Mitchell et al., 2019) are standardised documentation that accompany deployed models. They create the transparency and accountability record needed for: (1) informed deployment decisions, (2) regulatory auditing, (3) identifying inappropriate uses, and (4) understanding subgroup performance disparities.'],
            // 18.10 — Building ethical practice
            ['q' => 'GDPR Article 25 makes which design principle a legal requirement?', 'opts' => ['Explainability by Design', 'Privacy by Design and by Default — privacy must be embedded into system architecture from the start, not added as an afterthought', 'Security by Obscurity', 'Fairness by Design for all automated systems'], 'ans' => 1, 'exp' => 'GDPR Article 25 codifies Privacy by Design (developed by Ann Cavoukian) into law. It requires controllers to implement appropriate technical and organisational measures to integrate data protection principles into processing systems from design stage — including making the most privacy-protective setting the default configuration.'],
            ['q' => 'A data scientist discovers significant racial bias in a production model but their manager dismisses the concern. What is the most ethically appropriate course of action?', 'opts' => ['Accept the manager\'s judgement and close the issue', 'Document the finding formally in writing and escalate through appropriate channels including legal, compliance, or an ethics committee — silent compliance makes the data scientist morally responsible for the ongoing harm', 'Quietly fix the model without documentation to avoid conflict', 'Wait for an external audit to raise the issue'], 'ans' => 1, 'exp' => 'When a data scientist identifies a known harm, professional and ethical responsibility requires documentation and formal escalation — not silent compliance. Writing it down creates a record; escalating to legal/compliance ensures expert assessment. Many of the most serious AI ethics failures were internally known and silently tolerated. Silent compliance is not ethical neutrality — it is complicity.'],
        ];

        $finalContent = <<<HTML
<div id="org-lock-screen" style="text-align:center;padding:4rem 2rem;background:var(--surface2);border:1px solid var(--border);border-radius:12px;margin-top:2rem;">
    <div style="font-size:3rem;margin-bottom:1rem;">🔒</div>
    <h3 style="color:var(--text);margin-bottom:0.5rem;">University / Organization Access Only</h3>
    <p style="color:var(--muted);">The Final Module Exam is restricted to enrolled students and verified organization members.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 18: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 18.1 through 18.10 — ethics foundations, personal data taxonomy, GDPR, global privacy laws, algorithmic bias, fairness frameworks, privacy-enhancing technologies, data governance, responsible AI, and building an ethical practice. Good luck!</p>
HTML;

        $finalContent .= $this->appendQuiz('', 'FINAL_EXAM', $allFinalQuestions);
        $finalContent .= '</div>';
        $finalContent .= <<<HTML
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.USER_ORG_ID !== 'undefined' && window.USER_ORG_ID !== null && window.USER_ORG_ID !== '') {
        document.getElementById('org-lock-screen').style.display = 'none';
        document.getElementById('final-exam-content').style.display = 'block';
    }
});
</script>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '18.11 Final Exam: Privacy, Ethics & Governance Mastery',
            'order_index' => 11,
            'content'     => $finalContent,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────

    /**
     * Generates the full Quiz HTML/CSS/JS block and appends it to $htmlContent.
     */
    private function appendQuiz(string $htmlContent, string $quizPrefix, array $questions): string
    {
        $total   = count($questions);
        $letters = ['A', 'B', 'C', 'D', 'E'];

        $html  = $htmlContent;
        $html .= '<style>
            .quiz-wrapper{display:flex;flex-direction:column;gap:24px;margin-top:40px;}
            .quiz-card{background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;}
            .quiz-card-header{background:rgba(0,0,0,0.2);padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;gap:12px;}
            .quiz-q-num{background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:"JetBrains Mono",monospace;white-space:nowrap;margin-top:2px;}
            .quiz-q-text{font-size:0.95rem;font-weight:600;color:var(--text);line-height:1.5;}
            .quiz-options{padding:16px 20px;display:flex;flex-direction:column;gap:10px;}
            .quiz-option{display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-radius:7px;border:1px solid var(--border);cursor:pointer;transition:all 0.15s;font-size:0.875rem;color:var(--muted);background:transparent;text-align:left;width:100%;font-family:"Inter",sans-serif;}
            .quiz-option:hover:not(.locked){border-color:var(--border-hover);background:var(--bg);color:var(--text);}
            .quiz-option .opt-key{width:22px;height:22px;border-radius:4px;border:1px solid var(--dim);font-size:0.7rem;font-weight:700;font-family:"JetBrains Mono",monospace;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;transition:all 0.15s;}
            .quiz-option.correct{border-color:#10b981;background:rgba(16,185,129,0.08);color:var(--text);}
            .quiz-option.correct .opt-key{background:#10b981;border-color:#10b981;color:#fff;}
            .quiz-option.wrong{border-color:#ef4444;background:rgba(239,68,68,0.08);color:var(--muted);opacity:0.7;}
            .quiz-option.locked{cursor:default;}
            .quiz-explanation{display:none;margin:0 20px 20px;padding:14px 16px;background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.25);border-radius:7px;font-size:0.875rem;color:var(--muted);line-height:1.7;}
            .quiz-explanation strong{color:var(--text);}
            .quiz-score-bar{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:var(--surface2);border:1px solid var(--border);border-radius:10px;font-size:0.875rem;color:var(--muted);font-weight:600;}
            .quiz-score-val{font-size:1.1rem;font-weight:700;color:#f59e0b;font-family:"JetBrains Mono",monospace;}
        </style>';

        $html .= '<div class="quiz-wrapper" id="wrap_' . $quizPrefix . '">';
        $html .= '<div class="quiz-score-bar"><span>Knowledge Check</span><span class="quiz-score-val"><span id="score_' . $quizPrefix . '">0</span> / ' . $total . '</span></div>';

        foreach ($questions as $qIndex => $q) {
            $qNum = $qIndex + 1;
            $qId  = $quizPrefix . '_q' . $qNum;

            $html .= '<div class="quiz-card" id="' . $qId . '">';
            $html .= '<div class="quiz-card-header"><span class="quiz-q-num">Q' . $qNum . '</span><span class="quiz-q-text">' . htmlspecialchars($q['q']) . '</span></div>';
            $html .= '<div class="quiz-options">';

            foreach ($q['opts'] as $optIndex => $option) {
                $isCorrect = ($optIndex === $q['ans']) ? 'true' : 'false';
                $letter    = $letters[$optIndex];
                $html .= '<button class="quiz-option" onclick="checkAnswer(this,\'' . $qId . '\',' . $isCorrect . ',\'' . $quizPrefix . '\')"><span class="opt-key">' . $letter . '</span> ' . htmlspecialchars($option) . '</button>';
            }

            $html .= '</div>';
            $html .= '<div class="quiz-explanation" id="' . $qId . '-exp"><strong>Explanation:</strong> ' . $q['exp'] . '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';

        $html .= "
<script>
if(typeof window.answeredQuizzes==='undefined'){window.answeredQuizzes={};}
if(typeof window.quizScores==='undefined'){window.quizScores={};}
window.checkAnswer=function(btn,qId,isCorrect,prefix){
    if(window.answeredQuizzes[qId])return;
    window.answeredQuizzes[qId]=true;
    if(typeof window.quizScores[prefix]==='undefined')window.quizScores[prefix]=0;
    const card=document.getElementById(qId);
    const allOpts=card.querySelectorAll('.quiz-option');
    allOpts.forEach(o=>o.classList.add('locked'));
    if(isCorrect){
        btn.classList.add('correct');
        window.quizScores[prefix]++;
    } else {
        btn.classList.add('wrong');
        allOpts.forEach(o=>{if(o.getAttribute('onclick').includes(',true,'))o.classList.add('correct');});
    }
    document.getElementById(qId+'-exp').style.display='block';
    document.getElementById('score_'+prefix).textContent=window.quizScores[prefix];
};
</script>
";

        return $html;
    }
}