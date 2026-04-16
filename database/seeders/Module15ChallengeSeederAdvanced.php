<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module15ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 15 — Data Visualization (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Data Visualization',
            'description'           => 'Advanced data visualization challenges — debug broken chart pipelines, optimize rendering for large datasets, reason through complex Plotly and Matplotlib customization, interpret multi-layered statistical charts, and resolve edge cases in subplot layouts, color encoding, and interactive widget design.',
            'time_limit_seconds'    => 1800, // 30 minutes
            'base_xp'               => 1500,
            'order_index'           => 15,
        ]);

        $this->command->info("Seeding 50 advanced-level questions...");

        $qaData = [

            // ── 15.1 INTRODUCTION TO DATA VISUALIZATION ───────────────────
            [
                'q' => "Find the bug in this code intended to display a chart:\n\nimport matplotlib.pyplot as plt\nfig, ax = plt.subplots()\nax.plot([1, 2, 3], [4, 5, 6])\nfig.savefig('output.png')\nplt.show()",
                'opts' => [
                    ['savefig() must be called after show(), not before', false],
                    ['There is no bug — savefig() before show() works correctly, but show() may display a blank figure in some backends because the figure was already rendered to file', true],
                    ['ax.plot() requires keyword arguments x= and y=', false],
                    ['fig.savefig() does not exist — use plt.savefig()', false],
                ],
            ],
            [
                'q' => "Why does the following code produce a blank white figure?\n\nimport matplotlib.pyplot as plt\nfig, ax = plt.subplots()\nax.plot([1,2,3],[4,5,6])\nplt.close()\nplt.show()",
                'opts' => [
                    ['plt.close() before plt.show() closes the figure so there is nothing to display', true],
                    ['ax.plot() must be called on plt directly, not on ax', false],
                    ['plt.subplots() always creates an invisible figure', false],
                    ['show() requires a filename argument to work', false],
                ],
            ],
            [
                'q' => "A Matplotlib chart is embedded in a Flask web app. Users report the chart image is sometimes blank when accessed by multiple users concurrently. What is the likely cause?",
                'opts' => [
                    ['Flask cannot serve PNG files from Matplotlib', false],
                    ['Matplotlib uses a global state — concurrent requests share the same figure state, causing race conditions. Use the Agg backend and create a new Figure object per request.', true],
                    ['PNG export is not thread-safe in Python 3.10+', false],
                    ['The chart needs to be converted to SVG before serving', false],
                ],
            ],

            // ── 15.2 MATPLOTLIB: LINE & BAR CHARTS ────────────────────────
            [
                'q' => "What is the output of the following code?\n\nimport matplotlib.pyplot as plt\nimport numpy as np\nx = np.linspace(0, 2 * np.pi, 100)\nplt.plot(x, np.sin(x), label='sin')\nplt.plot(x, np.cos(x), label='cos')\nplt.legend(loc='lower left')\nplt.show()",
                'opts' => [
                    ['Two overlapping scatter plots of sin and cos', false],
                    ['A smooth multi-line chart showing sin and cos curves over [0, 2π] with a legend in the lower left', true],
                    ['An error because np.sin and np.cos return NaN for values > π', false],
                    ['A single line that alternates between sin and cos values', false],
                ],
            ],
            [
                'q' => "A developer creates a bar chart in a loop, adding a bar for each row in a DataFrame with 10,000 rows. The chart renders but is completely unreadable. What is the real problem and best solution?",
                'opts' => [
                    ['Matplotlib cannot handle more than 100 bars — switch to Plotly', false],
                    ['10,000 individual bars is a design problem — aggregate the data first (e.g., by category or time period) before visualizing, reducing bars to a meaningful count', true],
                    ['The loop syntax is incorrect — use vectorized plt.bar() calls', false],
                    ['Increase figsize to (1000, 50) to accommodate all bars', false],
                ],
            ],
            [
                'q' => "What does the following code produce and why is it problematic?\n\nfig, ax = plt.subplots()\nfor i in range(1000):\n    ax.plot([i, i+1], [i**2, (i+1)**2])\nplt.show()",
                'opts' => [
                    ['A single parabola made of 1000 connected line segments — visually correct and efficient', false],
                    ['A parabola drawn using 1000 separate line objects, which is memory-intensive and slow — better to plot the full curve with a single ax.plot() call on all x/y arrays', true],
                    ['An error because plot() cannot be called inside a loop', false],
                    ['1000 separate charts displayed in sequence', false],
                ],
            ],

            // ── 15.3 HISTOGRAMS, BOX PLOTS & DISTRIBUTIONS ────────────────
            [
                'q' => "What does the following code produce?\n\nimport seaborn as sns\nimport matplotlib.pyplot as plt\nsns.histplot(df['age'], kde=True, bins=30, color='steelblue')\nplt.axvline(df['age'].mean(), color='red', linestyle='--', label='Mean')\nplt.axvline(df['age'].median(), color='green', linestyle='-', label='Median')\nplt.legend()\nplt.show()",
                'opts' => [
                    ['A histogram of age with a KDE curve, a red dashed vertical line at the mean, and a green solid line at the median', true],
                    ['A box plot with mean and median lines highlighted', false],
                    ['Two separate charts — one for mean and one for median', false],
                    ['An error because axvline cannot appear on a histogram', false],
                ],
            ],
            [
                'q' => "You want to compare the distributions of exam scores across 5 different schools on a single chart. Which is the MOST informative chart?\n\nSchools: A, B, C, D, E\nEach has 200 student scores.",
                'opts' => [
                    ['Five separate histograms in a grid', false],
                    ['A grouped violin plot (`sns.violinplot(x="school", y="score", data=df)`) showing full distribution shape per school', true],
                    ['A single histogram with all 1000 scores combined', false],
                    ['A pie chart showing each school\'s share of total students', false],
                ],
            ],
            [
                'q' => "What is the difference between `sns.histplot` and the deprecated `sns.distplot`?",
                'opts' => [
                    ['histplot only shows bars; distplot shows only the KDE curve', false],
                    ['histplot is the modern replacement offering count/density/probability normalization and better KDE integration; distplot mixed stat types inconsistently and is deprecated', true],
                    ['They are identical — distplot was just renamed to histplot', false],
                    ['distplot is faster for large datasets; histplot is for small ones', false],
                ],
            ],

            // ── 15.4 SCATTER PLOTS & CORRELATION ANALYSIS ─────────────────
            [
                'q' => "What does `sns.jointplot(x='height', y='weight', data=df, kind='reg')` produce?",
                'opts' => [
                    ['A joint scatter plot in the center with marginal histograms on the top and right axes, and a regression line', true],
                    ['A scatter plot split into two panels — one for each variable', false],
                    ['A regression table printed to the console', false],
                    ['A joint KDE density plot with no scatter points', false],
                ],
            ],
            [
                'q' => "A scatter plot of 500,000 data points is completely overplotted (solid black blob). Which TWO techniques BEST address this?\n\nPick the most complete answer.",
                'opts' => [
                    ['Increase marker size and use brighter colors', false],
                    ['Use alpha transparency (alpha=0.05) and/or switch to a 2D KDE/hexbin plot to show density instead of individual points', true],
                    ['Reduce the dataset to 100 rows by random sampling without consideration of representativeness', false],
                    ['Use a line chart instead — scatter plots cannot handle more than 10,000 points', false],
                ],
            ],
            [
                'q' => "What does `plt.hexbin(x, y, gridsize=30, cmap='Blues')` create and when should you use it?",
                'opts' => [
                    ['A hexagonal pie chart with 30 slices', false],
                    ['A 2D histogram using hexagonal bins to show point density — ideal for large datasets where scatter overplotting makes individual points invisible', true],
                    ['A scatter plot with hexagonal markers', false],
                    ['A Voronoi diagram with 30 regions', false],
                ],
            ],
            [
                'q' => "What does the Spearman correlation capture that Pearson correlation misses, and when does this matter for scatter plot interpretation?",
                'opts' => [
                    ['Spearman detects linear relationships; Pearson detects non-linear ones', false],
                    ['Spearman measures monotonic relationships (consistently increasing or decreasing, not necessarily linear), making it more appropriate when scatter plots show curved trends or contain outliers', true],
                    ['They are identical for data with more than 100 points', false],
                    ['Pearson captures rank-based relationships; Spearman is for normally distributed data only', false],
                ],
            ],

            // ── 15.5 HEATMAPS & CORRELATION MATRICES ──────────────────────
            [
                'q' => "A heatmap of a correlation matrix shows feature X with correlation 0.98 with feature Y. Both features are in a machine learning pipeline. What visualization-driven action should be taken?",
                'opts' => [
                    ['Keep both features — high correlation improves model accuracy', false],
                    ['Investigate and consider dropping one feature to reduce multicollinearity — use the heatmap to guide feature selection before training', true],
                    ['Apply PCA specifically to features X and Y only', false],
                    ['Normalize both features and recheck the heatmap before deciding', false],
                ],
            ],
            [
                'q' => "What is the output of this code?\n\nimport seaborn as sns\nimport pandas as pd\ndf = pd.DataFrame({'A':[1,2,3],'B':[4,5,6],'C':[7,8,9]})\nsns.heatmap(df.corr(), vmin=-1, vmax=1, center=0, cmap='RdBu')\n\nThe correlation between A, B, and C is 1.0 for all pairs. What does the heatmap look like?",
                'opts' => [
                    ['All cells are red since all correlations are +1.0', false],
                    ['A uniform dark red heatmap since all pairs have correlation 1.0 (the maximum), and vmax=1 with a diverging RdBu colormap maps 1.0 to the darkest red end', true],
                    ['All cells appear white because a correlation of 1.0 is centered at 0', false],
                    ['An error because the correlation matrix of perfectly correlated features is singular', false],
                ],
            ],
            [
                'q' => "When using `sns.heatmap()` on a large (50×50) matrix, rendering is slow. Which approach improves performance without losing insight?",
                'opts' => [
                    ['Increase the figure DPI to 300 for faster rendering', false],
                    ['Disable `annot=True` (removes text per cell), reduce figsize, and consider clustering rows/columns with `sns.clustermap()` to reveal structure more efficiently', true],
                    ['Convert the DataFrame to a NumPy array before passing to heatmap', false],
                    ['Use plt.imshow() directly — it is identical to sns.heatmap() but faster', false],
                ],
            ],

            // ── 15.6 PAIR PLOTS & FACETGRIDS ──────────────────────────────
            [
                'q' => "What does `sns.clustermap(df.corr(), cmap='coolwarm', figsize=(10,10))` do differently from `sns.heatmap(df.corr())`?",
                'opts' => [
                    ['It is identical but renders in a cluster of windows', false],
                    ['It reorders rows and columns using hierarchical clustering so related features appear adjacent, revealing feature groupings that a standard heatmap does not', true],
                    ['It applies k-means clustering to the data before computing correlations', false],
                    ['It creates one heatmap per cluster group', false],
                ],
            ],
            [
                'q' => "A FacetGrid with `col='year'` produces 15 columns for 15 years, making the chart unreadably wide. What is the correct fix?",
                'opts' => [
                    ['Remove years from the chart — FacetGrid cannot handle more than 5 columns', false],
                    ['Add `col_wrap=5` to wrap columns into multiple rows of 5 charts each', true],
                    ['Use `col_order` to rearrange years in a different order', false],
                    ['Switch from FacetGrid to a single wide line chart', false],
                ],
            ],
            [
                'q' => "What does the following FacetGrid code produce?\n\ng = sns.FacetGrid(df, row='gender', col='education', margin_titles=True)\ng.map(sns.histplot, 'income', bins=20)",
                'opts' => [
                    ['A single histogram of income filtered by gender and education', false],
                    ['A grid of income histograms where each row is a gender level and each column is an education level, showing 20-bin income distributions for every combination', true],
                    ['A scatter plot of gender vs. education colored by income', false],
                    ['An error because FacetGrid cannot accept both row and col parameters', false],
                ],
            ],

            // ── 15.7 PIE, DONUT & PART-TO-WHOLE CHARTS ────────────────────
            [
                'q' => "A pie chart with 8 slices has two slices that each represent less than 2% of the total. What is the BEST visualization remedy?",
                'opts' => [
                    ['Remove those slices and recalculate percentages for the remaining ones', false],
                    ['Group the two small slices into an "Other" category, or switch to a bar chart that accurately represents small differences', true],
                    ['Make those two slices larger using the explode parameter for visibility', false],
                    ['Add a secondary legend table below the pie chart listing their exact values', false],
                ],
            ],
            [
                'q' => "Which of the following code snippets correctly creates a donut chart with a centered text label showing the total?\n\ntotal = sum(sizes)",
                'opts' => [
                    ["plt.pie(sizes, wedgeprops={'width':0.5})\nplt.text(0, 0, str(total), ha='center', va='center', fontsize=18)", true],
                    ["plt.donut(sizes, center_label=str(total))", false],
                    ["plt.pie(sizes, hole=0.5, label=str(total))", false],
                    ["plt.ring(sizes)\nplt.center_text(total)", false],
                ],
            ],
            [
                'q' => "What is a Waffle chart and when is it preferred over a pie chart?",
                'opts' => [
                    ['A waffle chart is a 3D pie chart — preferred for round numbers', false],
                    ['A waffle chart represents proportions as filled squares in a grid (e.g. 10×10), making it easier to estimate percentages accurately without the angular distortion of pie charts', true],
                    ['A waffle chart is a stacked bar chart variant from Plotly', false],
                    ['A waffle chart is used exclusively for binary data (two categories)', false],
                ],
            ],

            // ── 15.8 SUBPLOTS, LAYOUTS & CUSTOMIZATION ────────────────────
            [
                'q' => "What does `gridspec_kw={'height_ratios': [2, 1]}` do in `plt.subplots(2, 1, gridspec_kw=...)`?",
                'opts' => [
                    ['Creates 2 subplots where the top subplot is twice as tall as the bottom subplot', true],
                    ['Sets the total figure height to 2 and width to 1', false],
                    ['Applies a 2:1 aspect ratio to each subplot independently', false],
                    ['Raises a TypeError — gridspec_kw is not a valid parameter for subplots()', false],
                ],
            ],
            [
                'q' => "What is the bug in this twin-axis code?\n\nfig, ax1 = plt.subplots()\nax2 = ax1.twinx()\nax1.plot(x, temperature, color='red')\nax2.plot(x, rainfall, color='blue')\nplt.legend()\nplt.show()",
                'opts' => [
                    ['twinx() should be twiny() for a second y-axis', false],
                    ['plt.legend() only captures ax1\'s artists — ax2\'s line is missing from the legend. Use combined legend handles from both axes.', true],
                    ['Two axes cannot have different colors', false],
                    ['twinx() must be called on fig, not ax1', false],
                ],
            ],
            [
                'q' => "What does `matplotlib.gridspec.GridSpec` allow you to do that `plt.subplots()` does not?",
                'opts' => [
                    ['Create charts with more than 100 subplots', false],
                    ['Create subplots with varying sizes and spans — e.g., one subplot spanning 2 columns while others occupy 1 each', true],
                    ['Render charts faster than standard subplots', false],
                    ['Export subplots to individual files automatically', false],
                ],
            ],
            [
                'q' => "What does `plt.rcParams['figure.dpi'] = 150` do?",
                'opts' => [
                    ['Sets the display resolution for all subsequent Matplotlib figures to 150 dots per inch, affecting clarity when saving or rendering', true],
                    ['Reduces the memory used by figures by 150%', false],
                    ['Sets the default figure size to 150×150 pixels', false],
                    ['Applies to only the current figure, not future ones', false],
                ],
            ],

            // ── 15.9 INTERACTIVE CHARTS WITH PLOTLY ───────────────────────
            [
                'q' => "What does the following Plotly code achieve?\n\nimport plotly.graph_objects as go\nfig = go.Figure()\nfor country in df['country'].unique():\n    subset = df[df['country'] == country]\n    fig.add_trace(go.Scatter(x=subset['year'], y=subset['gdp'],\n                             mode='lines', name=country))\nfig.show()",
                'opts' => [
                    ['A separate Plotly figure per country', false],
                    ['A single interactive multi-line chart where each country is a separately named, togglable trace', true],
                    ['A static Matplotlib chart mimicked by Plotly', false],
                    ['An error because add_trace() cannot be used in a loop', false],
                ],
            ],
            [
                'q' => "What does `fig.update_traces(marker=dict(size=10, opacity=0.7, line=dict(width=1, color='black')))` do in a Plotly scatter chart?",
                'opts' => [
                    ['Sets all scatter markers to size 10, 70% opacity, and adds a 1px black border around each dot', true],
                    ['Updates the figure\'s background to be 70% transparent', false],
                    ['Applies changes only to the first trace, not all traces', false],
                    ['Raises an error because marker properties require a loop', false],
                ],
            ],
            [
                'q' => "A Plotly dashboard renders slowly because a chart uses a DataFrame with 2 million rows. What is the BEST optimization?",
                'opts' => [
                    ['Switch from Plotly Express to graph_objects for faster rendering', false],
                    ['Pre-aggregate or downsample the data before passing to Plotly — Plotly renders all data points client-side in JavaScript, so reducing data volume is the most effective optimization', true],
                    ['Increase the server RAM so JavaScript can process more data', false],
                    ['Convert the DataFrame to a JSON string before passing it to Plotly', false],
                ],
            ],
            [
                'q' => "What is the correct way to create an animated Plotly line chart showing GDP change over years?\n\ndf has columns: country, year, gdp",
                'opts' => [
                    ["px.line(df, x='country', y='gdp', animation_frame='year')", false],
                    ["px.line(df, x='year', y='gdp', color='country', animation_frame='year', animation_group='country')", true],
                    ["px.animate(df, x='year', y='gdp', frame='country')", false],
                    ["go.Figure(frames=[go.Frame(data=df[df.year==y]) for y in df.year])", false],
                ],
            ],

            // ── 15.10 BEST PRACTICES, COLOR & STORYTELLING ────────────────
            [
                'q' => "A chart uses the 'jet' colormap for a continuous sequential variable. Why is this considered bad practice?",
                'opts' => [
                    ['The jet colormap is only available in older Matplotlib versions', false],
                    ['Jet is perceptually non-uniform — the same value steps produce different apparent color differences in different regions, distorting interpretation. Use perceptually uniform colormaps like "viridis" or "plasma" instead.', true],
                    ['Jet maps all values to shades of blue, making it too monochrome', false],
                    ['Jet is a diverging colormap and should only be used for data centered at zero', false],
                ],
            ],
            [
                'q' => "You are building a chart that will be printed in black and white. Which chart design choice is MOST important?",
                'opts' => [
                    ['Use as many colors as possible to ensure visual richness', false],
                    ['Encode different series using different line styles (solid, dashed, dotted) and marker shapes rather than relying on color alone', true],
                    ['Add a color legend and note that printing should be in color', false],
                    ['Use the "gray" colormap for all elements', false],
                ],
            ],
            [
                'q' => "What is a Sparkline and when is it used in professional reporting?",
                'opts' => [
                    ['A special Seaborn chart type for real-time data', false],
                    ['A very small line chart embedded inline in text or tables to show trends without axes or labels — used to give compact context alongside summary statistics in dashboards and reports', true],
                    ['An animated line chart that updates in real time', false],
                    ['A horizontal bar chart reduced to a single pixel width', false],
                ],
            ],
            [
                'q' => "What does `plt.colorbar()` require in order to display correctly on a Matplotlib chart?",
                'opts' => [
                    ['plt.colorbar() can only be added after plt.show()', false],
                    ['A mappable object (such as the return value of plt.scatter() with c= or plt.imshow()) must be passed or be the most recent mappable — otherwise it raises a RuntimeError', true],
                    ['The chart must use the Seaborn backend', false],
                    ['A DataFrame with a color column must be provided as input', false],
                ],
            ],
            [
                'q' => "A visualization report has 10 charts where each one answers a different business question. An executive reviewing it complains it is impossible to follow. What storytelling principle is violated?",
                'opts' => [
                    ['The charts use too many different libraries', false],
                    ['The charts lack a narrative flow — effective data storytelling sequences visualizations to build from context → insight → recommendation, not as disconnected answers', true],
                    ['There are too many charts — the maximum for a report is 5', false],
                    ['All charts should use the same chart type for consistency', false],
                ],
            ],
            [
                'q' => "What is the purpose of `plt.tight_layout(rect=[0, 0, 1, 0.96])` when adding a suptitle?",
                'opts' => [
                    ['Scales the entire figure to 96% of screen height', false],
                    ['Reserves space at the top (4% of figure height) so the figure super-title added by plt.suptitle() does not overlap with the subplot titles', true],
                    ['Compresses subplots to fit within a 0 to 0.96 y-range on the screen', false],
                    ['Raises an error because rect conflicts with tight_layout\'s auto-adjustment', false],
                ],
            ],
            [
                'q' => "What is the difference between `fig.suptitle()` and `ax.set_title()` in a multi-subplot figure?",
                'opts' => [
                    ['They are identical — both set the main title of the figure', false],
                    ['fig.suptitle() sets a single title for the entire figure above all subplots; ax.set_title() sets the title for an individual subplot only', true],
                    ['suptitle() is deprecated — use ax.set_title() for all cases', false],
                    ['ax.set_title() sets the figure title; suptitle() sets the axis label', false],
                ],
            ],
            [
                'q' => "Why does calling `sns.set_theme()` at the start of a notebook affect Matplotlib plots that come after it?",
                'opts' => [
                    ['It only affects Seaborn plots — Matplotlib is a separate library unaffected by Seaborn settings', false],
                    ['Seaborn modifies Matplotlib\'s rcParams globally when set_theme() is called, so all subsequent Matplotlib and Seaborn plots inherit the theme until rcParams are reset', true],
                    ['set_theme() creates a copy of Matplotlib with Seaborn defaults applied', false],
                    ['Only plots inside the same function call are affected', false],
                ],
            ],
            [
                'q' => "What does `sns.despine(ax=ax, top=True, right=True, left=False, bottom=False)` do?",
                'opts' => [
                    ['Removes all four chart borders (spines) for a cleaner look', false],
                    ['Removes only the top and right spines while keeping the left and bottom, producing a cleaner open-box axis style', true],
                    ['Flips the chart upside-down by swapping top and bottom spines', false],
                    ['Despine is not a valid Seaborn function', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 15 — Data Visualization (Advanced).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Advanced");
    }
}