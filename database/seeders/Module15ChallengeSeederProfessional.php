<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module15ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 15 — Data Visualization (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Data Visualization',
            'description'           => 'Professional-grade data visualization challenges — production dashboard design, accessibility standards, real-world rendering performance, Plotly Dash architecture, perceptual accuracy, publication-quality chart engineering, and visualization ethics. Expect ambiguous problems requiring trade-off reasoning and expert judgment.',
            'time_limit_seconds'    => 2400, // 40 minutes
            'base_xp'               => 2000,
            'order_index'           => 15,
        ]);

        $this->command->info("Seeding 50 professional-level questions...");

        $qaData = [

            // ── 15.1 INTRODUCTION TO DATA VISUALIZATION ───────────────────
            [
                'q' => "A company builds a real-time operations dashboard that queries a database and re-renders 20 Matplotlib charts every 5 seconds using a Flask cron endpoint. After 2 hours, the server runs out of memory. What is the root cause and correct fix?",
                'opts' => [
                    ['Flask is not designed for real-time dashboards — switch to Node.js', false],
                    ['Each render cycle creates new Figure objects that are never explicitly closed. Call plt.close(fig) after saving each figure to a buffer, or use a dedicated dashboard framework like Plotly Dash or Streamlit which manage figure lifecycle properly.', true],
                    ['Reduce DPI to 50 so each figure consumes less memory', false],
                    ['Use multithreading to render charts in parallel, freeing memory faster', false],
                ],
            ],
            [
                'q' => "You are designing a visualization system for a hospital dashboard displaying patient vitals. The audience includes nurses who are colorblind. Which combination of design decisions is MOST appropriate?",
                'opts' => [
                    ['Use red for danger and green for normal — standard medical color coding familiar to all staff', false],
                    ['Use the colorblind-safe "Wong" palette, encode status with both color AND shape/line-style, add text labels for critical values, and test with a color blindness simulator before deployment', true],
                    ['Use grayscale only since all colorblind variants can distinguish shades of gray', false],
                    ['Use animated blinking for critical values as the primary alert mechanism instead of color', false],
                ],
            ],
            [
                'q' => "A data visualization is described as 'chartjunk'. What does this term mean (coined by Edward Tufte), and which chart element exemplifies it?",
                'opts' => [
                    ['A chart with incorrect data — named after the garbage data it displays', false],
                    ['Visual elements that add no informational value but increase visual complexity — such as decorative 3D effects, unnecessary gridlines, background images, or excessive tick marks that distract from the data', true],
                    ['A chart produced by a beginner with no labels or title', false],
                    ['A pie chart with too many slices — the most commonly incorrect chart type', false],
                ],
            ],

            // ── 15.2 MATPLOTLIB: LINE & BAR CHARTS ────────────────────────
            [
                'q' => "You need to produce a publication-quality chart for a journal submission requiring 300 DPI, vector-quality lines, embedded fonts, and PDF output. Which Matplotlib configuration produces this correctly?",
                'opts' => [
                    ["plt.savefig('chart.pdf', dpi=300, bbox_inches='tight', format='pdf')\nplt.rcParams['pdf.fonttype'] = 42  # TrueType fonts embedded", true],
                    ["plt.savefig('chart.png', dpi=300, format='png')\n# PNG is vector quality at 300 DPI", false],
                    ["plt.savefig('chart.svg', dpi=300)  # SVG is always journal-ready", false],
                    ["plt.figure(dpi=300)\nplt.savefig('chart.pdf')", false],
                ],
            ],
            [
                'q' => "A financial analyst creates a chart showing stock prices. The y-axis starts at 450 instead of 0, making a 2% drop look like a 50% collapse. When is truncating the y-axis ACCEPTABLE in professional visualization?",
                'opts' => [
                    ['Never — the y-axis must always start at zero in professional charts', false],
                    ['When the range of values is naturally far from zero and the chart clearly labels the axis start value, and the goal is to highlight relative changes rather than absolute magnitudes — e.g., stock price movement charts where proportional change is the focus', true],
                    ['Always for financial data — absolute scale is irrelevant in finance', false],
                    ['Only when the values exceed 1,000 and zero would compress the visible range too much', false],
                ],
            ],
            [
                'q' => "A team uses Matplotlib to generate 500 individual bar charts (one per product) and saves each as a PNG. The process takes 45 minutes. What is the most effective optimization?",
                'opts' => [
                    ['Use multiprocessing.Pool to generate charts in parallel, reusing a single Figure object per worker with plt.cla() between renders', true],
                    ['Switch to Seaborn which renders faster than Matplotlib', false],
                    ['Reduce the number of products to 100', false],
                    ['Generate charts in a Jupyter notebook instead of a .py script for faster execution', false],
                ],
            ],

            // ── 15.3 HISTOGRAMS, BOX PLOTS & DISTRIBUTIONS ────────────────
            [
                'q' => "You create a KDE plot of household income data. The KDE curve extends into negative values (below $0), which is impossible for income. What causes this and how do you fix it?",
                'opts' => [
                    ['The dataset has negative values that should be removed as outliers', false],
                    ['KDE uses a symmetric kernel (e.g., Gaussian) that extends infinitely in both directions, producing negative artifacts near a boundary. Fix by using `bw_adjust` for narrower bandwidth, applying a log transformation before plotting, or using a bounded KDE with `cut=0` in sns.kdeplot()', true],
                    ['The bins parameter is set too high — reduce bins to 10', false],
                    ['KDE always extends below zero for right-skewed data — this is expected behavior', false],
                ],
            ],
            [
                'q' => "What does a raincloud plot combine, and why is it considered superior to a box plot in academic visualization?",
                'opts' => [
                    ['It combines a pie chart and a histogram — showing both proportions and distributions', false],
                    ['It combines a half-violin plot (distribution shape), a box plot (summary statistics), and a strip plot (raw data points), providing more information than any single chart type while avoiding the information loss of a box plot alone', true],
                    ['It adds animated rainfall effects to standard line charts for presentations', false],
                    ['It is a 3D scatter plot viewed from above to simulate clouds', false],
                ],
            ],
            [
                'q' => "A histogram of transaction amounts uses `bins=1000` for a dataset with 5,000 records. What problem does this create and what is the correct approach?",
                'opts' => [
                    ['Too many bins causes Matplotlib to crash', false],
                    ['Over-binning causes most bins to contain 0 or 1 observations, making the distribution appear as noise rather than shape. Apply Sturges\', Scott\'s, or Freedman-Diaconis rule to select an appropriate bin count, or use `bins="auto"` and validate visually.', true],
                    ['1,000 bins is optimal for 5,000 records — exactly 5 records per bin', false],
                    ['The histogram should use density=True to handle 1,000 bins correctly', false],
                ],
            ],

            // ── 15.4 SCATTER PLOTS & CORRELATION ANALYSIS ─────────────────
            [
                'q' => "You are visualizing the relationship between marketing spend and revenue across 50 companies. One company spent $10M with $2M revenue (clear outlier). The Pearson correlation is 0.45, but removing the outlier gives 0.82. What is the MOST responsible professional action?",
                'opts' => [
                    ['Remove the outlier — it is obviously an error and distorts the analysis', false],
                    ['Report both correlation values, investigate the outlier company (it may be valid data revealing a different business model), visualize with and without it, and note that the relationship is sensitive to this observation', true],
                    ['Report only the 0.82 correlation since it represents the true relationship', false],
                    ['Apply a Spearman correlation instead and report it without mentioning the outlier', false],
                ],
            ],
            [
                'q' => "A scatter plot of 10 million GPS coordinates is needed for a web dashboard. The browser crashes rendering it via Plotly. What is the CORRECT architecture?",
                'opts' => [
                    ['Pre-render the chart server-side with Matplotlib and serve as a PNG — no interactivity needed', false],
                    ['Use spatial binning (hexbin) or density-based downsampling on the server before sending to Plotly. Alternatively, use WebGL-accelerated Plotly traces (Scattergl) which handle millions of points client-side without crashing.', true],
                    ['Compress the DataFrame to Parquet before passing to Plotly — it renders Parquet faster', false],
                    ['Limit the dashboard to 10,000 points and discard the rest silently', false],
                ],
            ],
            [
                'q' => "What is Simpson's Paradox in the context of scatter plot visualization, and why is it dangerous?",
                'opts' => [
                    ['A phenomenon where scatter plots with more than 1,000 points appear to show no trend', false],
                    ['A statistical paradox where a trend appearing in combined data disappears or reverses when the data is separated by groups — dangerous because a single aggregate scatter plot may show the opposite conclusion from per-group plots', true],
                    ['When two scatter plots look identical but represent completely different datasets (Anscombe\'s Quartet)', false],
                    ['A situation where adding a regression line to a scatter plot introduces bias', false],
                ],
            ],

            // ── 15.5 HEATMAPS & CORRELATION MATRICES ──────────────────────
            [
                'q' => "A team creates a correlation heatmap for a dataset with 200 features and publishes it in a report. The heatmap is a 200×200 grid of tiny colored squares with no readable values. What is the correct professional approach?",
                'opts' => [
                    ['Increase the figure size to 200×200 inches to make each cell 1 inch', false],
                    ['Use hierarchical clustering (sns.clustermap) to group related features, report only the top N most correlated pairs as a ranked table, and show a reduced heatmap of feature clusters rather than all 200×200 cells', true],
                    ['Show the heatmap as-is — the audience can zoom in digitally', false],
                    ['Replace the heatmap with 200 separate bar charts showing each feature\'s correlation vector', false],
                ],
            ],
            [
                'q' => "Which colormap should be used for each of the following scenarios?\n\n1. Sequential data (e.g., population density: low to high)\n2. Diverging data (e.g., profit/loss centered at 0)\n3. Categorical/qualitative data (e.g., 8 product categories)",
                'opts' => [
                    ['1: jet, 2: coolwarm, 3: rainbow', false],
                    ['1: viridis or plasma, 2: RdBu or coolwarm, 3: tab10 or Set1', true],
                    ['1: gray, 2: bwr, 3: hsv', false],
                    ['1: hot, 2: seismic, 3: prism', false],
                ],
            ],
            [
                'q' => "What does `fmt='.2f'` do in `sns.heatmap(corr, annot=True, fmt='.2f')`, and what happens if you use `fmt='d'` on a float correlation matrix?",
                'opts' => [
                    ["fmt='.2f' formats annotations to 2 decimal places. fmt='d' (integer format) on floats raises a ValueError because it cannot format float values as integers.", true],
                    ["fmt='.2f' sets font size to 2; fmt='d' sets it to default", false],
                    ["fmt='.2f' and fmt='d' are interchangeable for float data", false],
                    ["fmt='d' converts all values to 0 or 1 before displaying", false],
                ],
            ],

            // ── 15.6 PAIR PLOTS & FACETGRIDS ──────────────────────────────
            [
                'q' => "A pair plot of a dataset with 15 numeric features and 50,000 rows takes 8 minutes to render. What is the MOST effective optimization strategy?",
                'opts' => [
                    ['Reduce figure DPI to 50', false],
                    ['Sample a representative subset (e.g., 2,000–5,000 rows using stratified sampling), select only the most relevant features (8 or fewer) based on domain knowledge or correlation analysis, and use `plot_kws={\"alpha\": 0.3}` for overplot reduction', true],
                    ['Use multiprocessing to render each pair in parallel', false],
                    ['Switch from sns.pairplot() to a manually built plt.subplots() grid for speed', false],
                ],
            ],
            [
                'q' => "A FacetGrid of 48 monthly charts (4 years × 12 months) is needed for a report. Users need to compare the same month across years. What layout and design choice is optimal?",
                'opts' => [
                    ['col=\'month\', row=\'year\', col_wrap=12 — 12 columns (months) × 4 rows (years) so the same month aligns vertically for cross-year comparison', true],
                    ['col=\'year\', row=\'month\' — 4 columns (years) × 12 rows (months)', false],
                    ['col=\'month\', col_wrap=6 — wrap months into 8 rows of 6', false],
                    ['Generate 48 separate figures and save as individual PNG files', false],
                ],
            ],
            [
                'q' => "What does `g.set_titles(col_template='{col_name}', row_template='{row_name}')` do on a Seaborn FacetGrid?",
                'opts' => [
                    ['Sets the main figure title using the column and row names combined', false],
                    ['Customizes the title format of each subplot to show only the column variable value and row variable value rather than the default verbose format like "col_var = value"', true],
                    ['Removes all subplot titles and replaces with variable names', false],
                    ['Raises a KeyError if the template keys don\'t match column names', false],
                ],
            ],

            // ── 15.7 PIE, DONUT & PART-TO-WHOLE CHARTS ────────────────────
            [
                'q' => "A product manager requests a pie chart comparing revenue share of 15 product categories. As a professional data scientist, what is your response and alternative recommendation?",
                'opts' => [
                    ['Deliver the pie chart as requested — the manager knows best', false],
                    ['Explain that 15-slice pie charts are perceptually inaccurate (human eyes cannot accurately judge angles for many similar-sized slices), and propose a horizontal bar chart sorted by revenue share, which enables accurate length-based comparison', true],
                    ['Create a 3D pie chart — the extra dimension makes 15 slices easier to read', false],
                    ['Group all categories below 5% into "Other" and deliver a 5-slice pie chart', false],
                ],
            ],
            [
                'q' => "A Sankey diagram is requested to show customer flow from acquisition channel through product tier to churn/retention. What Python library produces Sankey diagrams and what does the chart require as input?",
                'opts' => [
                    ['Matplotlib — requires a flow matrix and color list', false],
                    ['Plotly (go.Sankey) — requires source node indices, target node indices, value (flow amounts), and node labels defining each stage in the flow', true],
                    ['Seaborn (sns.sankey) — requires a tidy DataFrame with source, target, and value columns', false],
                    ['NetworkX renders Sankey diagrams as directed graphs automatically', false],
                ],
            ],
            [
                'q' => "What is a Treemap and when is it preferred over a pie chart for part-to-whole visualization?",
                'opts' => [
                    ['A hierarchical bar chart — preferred when categories have sub-categories', false],
                    ['A chart that divides a rectangle into smaller rectangles proportional to each category\'s value — preferred over pie charts when there are many categories and/or hierarchical structure (e.g., revenue by region → country → product), as area-based comparison is more accurate than angular comparison', true],
                    ['A map chart showing tree coverage by region', false],
                    ['A specialized histogram for forest ecology data', false],
                ],
            ],

            // ── 15.8 SUBPLOTS, LAYOUTS & CUSTOMIZATION ────────────────────
            [
                'q' => "You need a publication figure with: 1 large scatter plot (top, full width), 2 smaller histograms side by side (bottom left and bottom right). Which Matplotlib approach correctly creates this asymmetric layout?",
                'opts' => [
                    ["fig, axes = plt.subplots(2, 2)", false],
                    ["from matplotlib.gridspec import GridSpec\ngs = GridSpec(2, 2)\nfig = plt.figure(figsize=(12, 8))\nax1 = fig.add_subplot(gs[0, :])   # top row, full width\nax2 = fig.add_subplot(gs[1, 0])  # bottom left\nax3 = fig.add_subplot(gs[1, 1])  # bottom right", true],
                    ["fig, axes = plt.subplots(1, 3, gridspec_kw={'width_ratios': [2, 1, 1]})", false],
                    ["plt.subplot2grid((2,2), (0,0), colspan=2)\nplt.subplot2grid((2,2), (1,0))\nplt.subplot2grid((2,2), (1,1))", false],
                ],
            ],
            [
                'q' => "What does `plt.rcParams.update(plt.rcParamsDefault)` do and when should it be used?",
                'opts' => [
                    ['It imports default parameters from a config file', false],
                    ['It resets all Matplotlib rcParams to factory defaults — essential after sns.set_theme() or custom rcParams have been applied globally, to avoid theme bleed between separate chart-generating modules', true],
                    ['It only updates the current figure\'s parameters, not global settings', false],
                    ['It raises a DeprecationWarning in Matplotlib 3.6+', false],
                ],
            ],
            [
                'q' => "A chart requires a custom legend that doesn't correspond to any plotted line (e.g., explaining symbol meanings). How do you add a manual legend in Matplotlib?",
                'opts' => [
                    ['Manually edit the PNG after export using an image editor', false],
                    ["Use `matplotlib.patches` or `matplotlib.lines` to create proxy artist handles:\nfrom matplotlib.patches import Patch\nhandles = [Patch(color='red', label='High Risk'), Patch(color='green', label='Low Risk')]\nax.legend(handles=handles)", true],
                    ['Call plt.legend(labels=[\"High Risk\", \"Low Risk\"]) — Matplotlib assigns colors automatically', false],
                    ['Only legends corresponding to plotted elements are supported in Matplotlib', false],
                ],
            ],

            // ── 15.9 INTERACTIVE CHARTS WITH PLOTLY DASH ──────────────────
            [
                'q' => "In a Plotly Dash application, what is the role of `@app.callback` and why is it essential for interactive charts?",
                'opts' => [
                    ['It imports the callback module from Plotly Express', false],
                    ['It defines a Python function that is triggered when a specified Input component changes, updating specified Output components — enabling reactive, interactive charts where user actions (sliders, dropdowns) dynamically update figures without page reload', true],
                    ['It caches chart renders to avoid redundant computation', false],
                    ['It registers the Dash app with a Flask server for production deployment', false],
                ],
            ],
            [
                'q' => "A Plotly Dash dashboard callback re-queries a database and re-renders a complex chart every time a dropdown changes. With 100 concurrent users, the server becomes unresponsive. What is the correct architecture fix?",
                'opts' => [
                    ['Increase the number of Dash workers using gunicorn --workers 16', false],
                    ['Use dcc.Store to cache query results client-side or implement server-side caching (Flask-Caching or Redis) so repeated queries return cached results, and use background callbacks (dash.long_callback) for expensive computations', true],
                    ['Reduce chart complexity by removing all annotations', false],
                    ['Switch from Plotly Dash to Streamlit which handles concurrency automatically', false],
                ],
            ],
            [
                'q' => "What does `prevent_initial_call=True` do in a Plotly Dash callback decorator?",
                'opts' => [
                    ['Prevents the callback from being called after the user\'s first interaction', false],
                    ['Prevents the callback from firing on the initial page load, avoiding unnecessary computation before the user has interacted with the dashboard', true],
                    ['Prevents the callback from running more than once per session', false],
                    ['Prevents multiple callbacks from running concurrently', false],
                ],
            ],
            [
                'q' => "What is the difference between `dcc.Graph` and `dcc.Slider` in Plotly Dash, and how do they work together?",
                'opts' => [
                    ['Both are identical input components — dcc.Graph renders charts while dcc.Slider renders charts with a time axis', false],
                    ['dcc.Graph is an Output component that renders a Plotly figure; dcc.Slider is an Input component providing a numeric value. A callback connects them: when the slider value changes, the callback regenerates and outputs an updated figure to dcc.Graph', true],
                    ['dcc.Slider generates the data range for dcc.Graph automatically without a callback', false],
                    ['dcc.Graph handles both rendering and interaction — dcc.Slider is optional', false],
                ],
            ],

            // ── 15.10 BEST PRACTICES, COLOR & STORYTELLING ────────────────
            [
                'q' => "A data journalist publishes a visualization showing crime rates rising 400% over 5 years. On inspection, the absolute numbers rose from 10 to 50 crimes in a city of 2 million people. What visualization ethics principle is violated?",
                'opts' => [
                    ['The wrong chart type was used', false],
                    ['Misleading use of relative change without absolute context — responsible visualization requires showing both relative and absolute numbers, and rates per capita, to give the audience the full picture rather than an alarming but potentially insignificant percentage', true],
                    ['The y-axis should start at zero, not the minimum value', false],
                    ['Crime data should never be visualized due to privacy concerns', false],
                ],
            ],
            [
                'q' => "You are building a visualization for a predictive model's outputs showing confidence intervals. Which chart element is ESSENTIAL and often missing from model output charts?",
                'opts' => [
                    ['Animated transitions between predictions', false],
                    ['Uncertainty representation — confidence intervals, credible intervals, or prediction intervals shown as shaded bands or error bars, so the audience understands the range of likely outcomes rather than treating point estimates as certainties', true],
                    ['A color scale showing probability values from 0 to 1', false],
                    ['A secondary axis showing the raw input features', false],
                ],
            ],
            [
                'q' => "What is Anscombe's Quartet and what critical lesson does it teach about summary statistics and visualization?",
                'opts' => [
                    ['A set of 4 identical-looking scatter plots with different underlying correlations', false],
                    ['Four datasets that have nearly identical summary statistics (mean, variance, correlation ~0.816) but look completely different when plotted — proving that summary statistics alone are insufficient and visualization is always necessary to understand data structure', true],
                    ['A set of 4 charts that each demonstrate a different chart type for the same dataset', false],
                    ['A checklist of 4 questions to ask before creating any visualization', false],
                ],
            ],
            [
                'q' => "A government agency wants to visualize vaccination rates across 1,800 municipalities on a single dashboard for policymakers. Which visualization architecture is MOST appropriate?",
                'opts' => [
                    ['A bar chart with 1,800 bars sorted alphabetically', false],
                    ['An interactive choropleth map with municipal-level shading by vaccination rate, a filterable Plotly Dash sidebar by region and rate threshold, a summary bar chart updating based on selection, and click-to-drill-down to individual municipality data — delivering spatial context, comparison, and detail in one coherent interface', true],
                    ['A single large table with 1,800 rows and download option', false],
                    ['1,800 individual pie charts embedded in a scrollable page', false],
                ],
            ],
            [
                'q' => "What is the \"dual-encoding\" principle in visualization design, and give an example of it applied correctly?",
                'opts' => [
                    ['Using two separate charts to show the same data from different angles', false],
                    ['Encoding the same variable with two visual channels simultaneously to reinforce perception — e.g., encoding both the color AND the size of scatter plot dots by a third variable "sales volume", so viewers perceive the variable even if one channel is ambiguous or inaccessible (e.g., for colorblind users)', true],
                    ['Creating two versions of the same chart: one for technical and one for non-technical audiences', false],
                    ['Displaying both a chart and its underlying data table side by side', false],
                ],
            ],
            [
                'q' => "You create a visualization showing that Company A's revenue grew 30% while Company B's grew 5%. A stakeholder insists this proves Company A is more successful. What critical context is missing from the visualization that could change this conclusion?",
                'opts' => [
                    ['The chart needs a second y-axis', false],
                    ['Absolute revenue base, industry context, and profitability — if Company A grew from $1M to $1.3M and Company B grew from $500M to $525M, Company B generated $24.7M more additional revenue. Growth rate without base context is a misleading metric. The visualization should show both percentage growth AND absolute values.', true],
                    ['The time period of measurement was not specified', false],
                    ['The chart should show quarterly rather than annual data', false],
                ],
            ],
            [
                'q' => "In the context of professional dashboards, what is the difference between a 'metric chart' (KPI card) and a 'trend chart', and why should they appear together?",
                'opts' => [
                    ['Metric charts show historical data; trend charts show future predictions', false],
                    ['A metric/KPI card shows the current value of a key indicator (a snapshot); a trend chart shows how that value has changed over time. Together they answer "What is the current state?" AND "Is it improving or worsening?" — neither alone gives complete context for decision-making.', true],
                    ['They are interchangeable — metric cards are just simplified trend charts', false],
                    ['Trend charts are for technical users; metric cards are for executives only', false],
                ],
            ],
            [
                'q' => "What does WebGL rendering in Plotly (`go.Scattergl` vs `go.Scatter`) enable, and what is its key limitation?",
                'opts' => [
                    ['WebGL renders charts server-side for faster delivery; the limitation is that it requires a GPU on the server', false],
                    ['WebGL uses the client\'s GPU to render scatter plots with millions of points at interactive frame rates. The key limitation is reduced support for certain hover/selection features and potential inconsistency across older browsers that lack WebGL support.', true],
                    ['WebGL converts Plotly charts to Canvas-based static images; the limitation is no interactivity', false],
                    ['WebGL allows 3D plotting; Scatter is only 2D — WebGL has no other advantages', false],
                ],
            ],
            [
                'q' => "A professional data team uses version control for code but not for charts. A stakeholder asks why the monthly revenue chart looks different from last month's report. What process should be implemented to prevent this?",
                'opts' => [
                    ['All charts should be hardcoded with fixed data to prevent changes', false],
                    ['Implement chart versioning: parameterize all chart configurations (data source, date range, filters, style), store parameters in config files under version control, generate charts deterministically from code, and archive rendered chart files alongside the code commit that produced them', true],
                    ['Use screenshots of all charts and store them in a shared drive', false],
                    ['Re-run all chart-generating code monthly to ensure consistency', false],
                ],
            ],

        ];

        foreach ($qaData as $data) {
            $question = ChallengeQuestion::create([
                'challenge_id'          => $challenge->id,
                'question_text'         => $data['q'],
                'challenge_category_id' => $category->id,
            ]);

            foreach ($data['opts'] as $opt) {
                ChallengeOption::create([
                    'challenge_question_id' => $question->id,
                    'option_text'           => $opt[0],
                    'is_correct'            => $opt[1],
                ]);
            }
        }

        $this->command->info("✅ Done! 50 questions seeded for Module 15 — Data Visualization (Professional).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Professional");
    }
}