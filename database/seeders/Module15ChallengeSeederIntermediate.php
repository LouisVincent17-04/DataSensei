<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module15ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 15 — Data Visualization (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Data Visualization',
            'description'           => 'Tackle intermediate-level data visualization problems — multi-step code tracing, subplot layouts, customization pipelines, statistical chart interpretation, and choosing the right combination of chart types and parameters for real analytical scenarios.',
            'time_limit_seconds'    => 1500, // 25 minutes
            'base_xp'               => 1000,
            'order_index'           => 15,
        ]);

        $this->command->info("Seeding 50 intermediate-level questions...");

        $qaData = [

            // ── 15.1 INTRODUCTION TO DATA VISUALIZATION ───────────────────
            [
                'q' => "What is the output of the following code?\n\nimport matplotlib.pyplot as plt\nfig, ax = plt.subplots()\nax.set_xlim(0, 10)\nax.set_ylim(-5, 5)\nax.axhline(0, color='black')\nplt.show()\n\nDescribe what is rendered.",
                'opts' => [
                    ['An empty plot with x from 0 to 10, y from -5 to 5, and a horizontal black line at y=0', true],
                    ['A plot that raises a ValueError because axhline needs data first', false],
                    ['A horizontal line across the entire screen at y=0 with no axes shown', false],
                    ['A scatter plot with a regression line', false],
                ],
            ],
            [
                'q' => "You need to create a visualization pipeline that:\n1. Loads a CSV with pandas\n2. Filters rows where sales > 1000\n3. Plots a bar chart of product vs filtered sales\n\nWhich step order is correct?",
                'opts' => [
                    ['Plot → Load → Filter', false],
                    ['Filter → Load → Plot', false],
                    ['Load → Filter → Plot', true],
                    ['Load → Plot → Filter', false],
                ],
            ],
            [
                'q' => "Which of the following correctly sets a Seaborn theme to 'darkgrid' AND changes the font scale to 1.4 before plotting?",
                'opts' => [
                    ["sns.set_style('darkgrid')\nsns.set(font_scale=1.4)", true],
                    ["sns.theme('darkgrid', font_scale=1.4)", false],
                    ["plt.style.use('darkgrid')\nplt.font_scale(1.4)", false],
                    ["sns.style('dark', scale=1.4)", false],
                ],
            ],

            // ── 15.2 MATPLOTLIB: LINE & BAR CHARTS ────────────────────────
            [
                'q' => "What does the following code produce?\n\nimport matplotlib.pyplot as plt\nx = [1, 2, 3, 4]\ny1 = [10, 20, 15, 25]\ny2 = [5, 15, 10, 20]\nplt.plot(x, y1, label='Product A')\nplt.plot(x, y2, label='Product B')\nplt.legend()\nplt.show()",
                'opts' => [
                    ['Two separate charts, one per product', false],
                    ['A single chart with two lines and a legend labeling each line', true],
                    ['A bar chart comparing Product A and Product B', false],
                    ['An error because two datasets cannot share one plot', false],
                ],
            ],
            [
                'q' => "A student wants a grouped bar chart comparing Male and Female scores across 3 subjects. Which approach is correct in Matplotlib?",
                'opts' => [
                    ['Call plt.bar() twice with offset x positions using numpy arrays and width adjustments', true],
                    ['Call plt.bar() twice with the same x positions — Matplotlib stacks them automatically', false],
                    ['Use plt.groupbar() with a group parameter', false],
                    ['Grouped bar charts require Seaborn — Matplotlib cannot create them', false],
                ],
            ],
            [
                'q' => "What does `plt.fill_between(x, y1, y2, alpha=0.3)` do?",
                'opts' => [
                    ['Draws a line from y1 to y2 at each x position', false],
                    ['Fills the area between two lines (y1 and y2) with a semi-transparent shaded region', true],
                    ['Blends the colors of y1 and y2 into a gradient', false],
                    ['Raises an error because fill_between requires 4 arguments', false],
                ],
            ],
            [
                'q' => "What is the correct way to plot a stacked bar chart where 'female' values are stacked on top of 'male' values?\n\ncategories = ['A', 'B', 'C']\nmale   = [30, 45, 20]\nfemale = [25, 35, 40]",
                'opts' => [
                    ["plt.bar(categories, male)\nplt.bar(categories, female)", false],
                    ["plt.bar(categories, male)\nplt.bar(categories, female, bottom=male)", true],
                    ["plt.stack([male, female], categories)", false],
                    ["plt.bar(categories, male + female)", false],
                ],
            ],

            // ── 15.3 HISTOGRAMS, BOX PLOTS & DISTRIBUTIONS ────────────────
            [
                'q' => "What does `sns.violinplot()` show that a standard box plot does NOT?",
                'opts' => [
                    ['The mean and median of the distribution', false],
                    ['The full probability density shape of the distribution in addition to the quartiles', true],
                    ['Outliers as individual dots outside the whiskers', false],
                    ['The correlation between two variables', false],
                ],
            ],
            [
                'q' => "A dataset of heights (cm) is plotted as a histogram with bins=[150, 155, 160, 165, 170, 175, 180]. The tallest bar is at the 165–170 bin with a count of 80. The next tallest is 160–165 with 60. What does this suggest?",
                'opts' => [
                    ['The mode of the dataset is between 165 and 170 cm', true],
                    ['The mean must be exactly 167.5 cm', false],
                    ['The dataset has a strong left skew', false],
                    ['There are 80 total data points in the dataset', false],
                ],
            ],
            [
                'q' => "What does `plt.hist(data, density=True)` change compared to `plt.hist(data)`?",
                'opts' => [
                    ['The y-axis shows probability density instead of raw counts, so the histogram integrates to 1', true],
                    ['It compresses the data to fit within [0, 1] on the x-axis', false],
                    ['It shows only the densest 50% of data points', false],
                    ['It switches from a histogram to a KDE plot', false],
                ],
            ],
            [
                'q' => "What does `sns.kdeplot(data)` visualize?",
                'opts' => [
                    ['A bar chart of key data events', false],
                    ['A smooth curve estimating the probability density function of the data', true],
                    ['A scatter plot of kernel coordinates', false],
                    ['A heatmap of data density across a 2D grid', false],
                ],
            ],
            [
                'q' => "You compare box plots of salary data for 5 departments. Department C has a much wider IQR than the others. What does this mean?",
                'opts' => [
                    ['Department C has the highest median salary', false],
                    ['Department C has the most outliers', false],
                    ['Department C has greater salary spread (more variability) among its employees than other departments', true],
                    ['The box plot for Department C was created with wrong data', false],
                ],
            ],

            // ── 15.4 SCATTER PLOTS & CORRELATION ANALYSIS ─────────────────
            [
                'q' => "A scatter plot of ice cream sales (y) vs. temperature (x) shows a strong positive trend. A student concludes that eating ice cream raises temperature. What is wrong?",
                'opts' => [
                    ['The student should have used a bar chart instead', false],
                    ['Correlation does not imply causation — both variables may be driven by season or time of year', true],
                    ['The scatter plot needs a regression line to establish causation', false],
                    ['The conclusion is correct if the correlation coefficient is above 0.9', false],
                ],
            ],
            [
                'q' => "What does `sns.regplot(x='age', y='income', data=df)` add compared to `sns.scatterplot()`?",
                'opts' => [
                    ['Color coding by category', false],
                    ['A fitted linear regression line with a confidence interval band', true],
                    ['A histogram in the margins', false],
                    ['Interactive hover tooltips', false],
                ],
            ],
            [
                'q' => "You create a scatter plot and notice a clear curved (non-linear) pattern instead of a straight line. Which chart addition BEST highlights this non-linearity?",
                'opts' => [
                    ['Add plt.axhline() at the mean', false],
                    ['Use sns.regplot() with `order=2` to fit a polynomial regression curve', true],
                    ['Increase the marker size with s=200', false],
                    ['Switch to a box plot', false],
                ],
            ],
            [
                'q' => "What does `plt.scatter(x, y, c=z, cmap='viridis')` do?\n\n(x, y, z are numeric arrays of the same length)",
                'opts' => [
                    ['Creates a 3D scatter plot with z as the depth axis', false],
                    ['Colors each dot by its z value using the viridis colormap, adding a third encoded variable', true],
                    ['Assigns random colors to dots using the viridis palette', false],
                    ['Raises a TypeError because scatter only accepts 2 positional arguments', false],
                ],
            ],

            // ── 15.5 HEATMAPS & CORRELATION MATRICES ──────────────────────
            [
                'q' => "What does `sns.heatmap(corr, mask=np.triu(np.ones_like(corr, dtype=bool)))` achieve?",
                'opts' => [
                    ['Shows only the upper triangle of the correlation matrix', false],
                    ['Shows only the lower triangle of the correlation matrix by masking the upper half', true],
                    ['Removes all positive correlations from the heatmap', false],
                    ['Applies a diagonal mask that hides the main diagonal', false],
                ],
            ],
            [
                'q' => "A heatmap of a 10×10 correlation matrix is hard to read. Which technique BEST improves readability?",
                'opts' => [
                    ['Increase figsize, show only the lower triangle, and use a diverging colormap like "coolwarm"', true],
                    ['Convert the heatmap to a pie chart', false],
                    ['Reduce annot font to 4pt so all values fit', false],
                    ['Switch from Seaborn to Matplotlib heatmap for better performance', false],
                ],
            ],
            [
                'q' => "You have a pivot table `df_pivot` with months as rows and products as columns, values are sales totals. What does the following produce?\n\nsns.heatmap(df_pivot, annot=True, fmt='.0f', cmap='YlGnBu')",
                'opts' => [
                    ['A scatter plot of sales by month and product', false],
                    ['A heatmap showing sales totals for each month-product combination with integer annotations', true],
                    ['A bar chart of total sales grouped by month', false],
                    ['An error because pivot tables cannot be passed to sns.heatmap()', false],
                ],
            ],

            // ── 15.6 PAIR PLOTS & FACETGRIDS ──────────────────────────────
            [
                'q' => "What does `sns.pairplot(df, diag_kind='kde')` change compared to the default `sns.pairplot(df)`?",
                'opts' => [
                    ['The off-diagonal plots change from scatter to KDE', false],
                    ['The diagonal plots change from histograms to KDE curves', true],
                    ['All plots become KDE density plots including the off-diagonal', false],
                    ['Nothing changes — diag_kind is ignored in pairplot', false],
                ],
            ],
            [
                'q' => "You create a FacetGrid to show monthly sales distributions for 12 months in a 3×4 grid. Which code correctly sets this up?",
                'opts' => [
                    ["g = sns.FacetGrid(df, col='month', col_wrap=4)\ng.map(sns.histplot, 'sales')", true],
                    ["sns.subplots(3, 4, data=df, x='month', y='sales')", false],
                    ["g = sns.FacetGrid(df, row=3, col=4)\ng.plot(histplot, 'sales')", false],
                    ["plt.facet(df, 'month', kind='hist', grid=(3,4))", false],
                ],
            ],
            [
                'q' => "In a pair plot of the Iris dataset with `hue='species'`, what does the diagonal KDE plot for the 'petal_length' column show?",
                'opts' => [
                    ['A scatter of petal_length against itself', false],
                    ['Overlapping KDE distributions of petal_length for each species, showing how each species differs', true],
                    ['The correlation value between petal_length and itself (always 1.0)', false],
                    ['A box plot of petal_length per species', false],
                ],
            ],

            // ── 15.7 PIE, DONUT & PART-TO-WHOLE CHARTS ────────────────────
            [
                'q' => "How do you create a donut chart in Matplotlib?\n\nlabels = ['A', 'B', 'C']\nsizes  = [40, 35, 25]",
                'opts' => [
                    ["plt.donut(sizes, labels=labels, hole=0.4)", false],
                    ["plt.pie(sizes, labels=labels, wedgeprops={'width': 0.5})", true],
                    ["plt.ring(sizes, labels=labels, inner=0.4)", false],
                    ["plt.pie(sizes, labels=labels, hole=True)", false],
                ],
            ],
            [
                'q' => "What does `autopct='%.1f%%'` do in `plt.pie(sizes, autopct='%.1f%%')`?",
                'opts' => [
                    ['Automatically colors each slice as a percentage of blue', false],
                    ['Displays the percentage value of each slice formatted to 1 decimal place inside the slice', true],
                    ['Converts raw values to percentages before plotting', false],
                    ['Shows the actual values instead of percentages', false],
                ],
            ],
            [
                'q' => "A pie chart has 12 slices for monthly revenue shares. Several slices are between 7% and 9%. What problem does this create?",
                'opts' => [
                    ['Matplotlib cannot render more than 10 slices', false],
                    ['Too many similarly-sized slices make it nearly impossible to distinguish or rank the months accurately', true],
                    ['The chart will crash because slices must differ by at least 5%', false],
                    ['The labels will overlap, but the chart is still accurate', false],
                ],
            ],

            // ── 15.8 SUBPLOTS, LAYOUTS & CUSTOMIZATION ────────────────────
            [
                'q' => "What does the following code create?\n\nfig = plt.figure(figsize=(12, 8))\nax1 = fig.add_subplot(2, 2, 1)\nax2 = fig.add_subplot(2, 2, 2)\nax3 = fig.add_subplot(2, 1, 2)",
                'opts' => [
                    ['Three subplots: two small ones in the top row and one wide one spanning the full bottom row', true],
                    ['A 2×2 grid with 4 equal subplots', false],
                    ['Three subplots stacked vertically', false],
                    ['An error because subplot positions overlap', false],
                ],
            ],
            [
                'q' => "What does `plt.subplots_adjust(hspace=0.5, wspace=0.3)` do?",
                'opts' => [
                    ['Sets the height and width of the entire figure', false],
                    ['Adjusts the vertical (hspace) and horizontal (wspace) spacing between subplots', true],
                    ['Resizes the fonts in all subplot titles', false],
                    ['Applies a uniform margin of 0.5 inches to the figure', false],
                ],
            ],
            [
                'q' => "You want a twin x-axis chart where the left y-axis shows temperature and the right y-axis shows rainfall on the same chart. Which code is correct?",
                'opts' => [
                    ["fig, ax1 = plt.subplots()\nax2 = ax1.twinx()\nax1.plot(months, temp)\nax2.bar(months, rain)", true],
                    ["fig, (ax1, ax2) = plt.subplots(1, 2)\nax1.plot(months, temp)\nax2.bar(months, rain)", false],
                    ["plt.plot(months, temp)\nplt.plot(months, rain, secondary=True)", false],
                    ["ax1 = plt.subplot(1)\nax2 = plt.subplot(2, twin=True)", false],
                ],
            ],
            [
                'q' => "What does `plt.annotate('Peak', xy=(5, 90), xytext=(6, 70), arrowprops=dict(arrowstyle='->'))`do?",
                'opts' => [
                    ['Adds a text label "Peak" at position (6, 70) with an arrow pointing to (5, 90)', true],
                    ['Draws an arrow from (5, 90) to (6, 70) with no text', false],
                    ['Creates a peak detection marker at the highest value in the chart', false],
                    ['Raises an error because annotate requires a patch object', false],
                ],
            ],

            // ── 15.9 INTERACTIVE CHARTS WITH PLOTLY ───────────────────────
            [
                'q' => "What does the following Plotly code create?\n\nimport plotly.express as px\nfig = px.scatter(df, x='gdp', y='life_expectancy',\n                 size='population', color='continent',\n                 hover_name='country')\nfig.show()",
                'opts' => [
                    ['A static scatter plot of GDP vs life expectancy', false],
                    ['An interactive bubble chart where dot size = population, color = continent, and hovering shows country name', true],
                    ['A heatmap of GDP by continent', false],
                    ['An error because scatter does not accept both size and color', false],
                ],
            ],
            [
                'q' => "What does `fig.update_layout(title='My Chart', xaxis_title='X', yaxis_title='Y')` do in Plotly?",
                'opts' => [
                    ['Creates a new figure replacing the existing one', false],
                    ['Updates the chart\'s title and axis labels on an existing Plotly figure', true],
                    ['Exports the figure with those labels to a PDF', false],
                    ['Only works on bar charts — not scatter or line charts', false],
                ],
            ],
            [
                'q' => "What is the difference between `fig.show()` and `fig.write_html('chart.html')` in Plotly?",
                'opts' => [
                    ['fig.show() saves to HTML; fig.write_html() displays in browser', false],
                    ['fig.show() displays the chart interactively in the current environment; fig.write_html() saves it as a standalone interactive HTML file', true],
                    ['They are identical — both save the file', false],
                    ['fig.write_html() requires an internet connection to render', false],
                ],
            ],
            [
                'q' => "How do you add a dropdown menu to select between different traces in a Plotly figure?",
                'opts' => [
                    ['Use `fig.add_dropdown(options=[...])`', false],
                    ['Use `fig.update_layout(updatemenus=[...])` with button definitions that toggle trace visibility', true],
                    ['Plotly does not support dropdown menus — use Dash instead', false],
                    ['Use `px.dropdown(fig, column=\'category\')`', false],
                ],
            ],

            // ── 15.10 BEST PRACTICES, COLOR & STORYTELLING ────────────────
            [
                'q' => "A visualization report shows 6 charts. The analyst uses 6 completely different color schemes for each chart. What is the best practice critique?",
                'opts' => [
                    ['Each chart should use different colors to avoid confusion', false],
                    ['Inconsistent color usage breaks visual continuity — the same category should use the same color throughout the report', true],
                    ['Six colors is too many — only 3 colors are allowed per report', false],
                    ['The issue is the number of charts, not the color scheme', false],
                ],
            ],
            [
                'q' => "You need to visualize the geographic sales distribution of a company across 30 Philippine provinces. Which chart type is MOST appropriate?",
                'opts' => [
                    ['A 30-slice pie chart', false],
                    ['A choropleth map where province regions are colored by sales intensity', true],
                    ['A bar chart with 30 bars sorted alphabetically', false],
                    ['A scatter plot with province names on the x-axis', false],
                ],
            ],
            [
                'q' => "What is wrong with adding 3D effects (depth, shadows) to a simple 2D bar chart in a business presentation?",
                'opts' => [
                    ['3D effects are only supported by Plotly, not Matplotlib', false],
                    ['3D effects add visual clutter and can distort the perceived height of bars, making comparisons less accurate', true],
                    ['The audience expects 3D visualizations in modern presentations', false],
                    ['Nothing — 3D always improves the visual appeal of charts', false],
                ],
            ],
            [
                'q' => "You are creating a visualization to tell the story of COVID-19 case trends over 2 years across 5 countries. Which combination is BEST?",
                'opts' => [
                    ['Five separate pie charts, one per country', false],
                    ['A multi-line chart with annotated key events (e.g., lockdowns, vaccine rollout) and a consistent color per country', true],
                    ['A heatmap of cases with countries on x and months on y, with no annotations', false],
                    ['A scatter plot of total cases vs. total deaths only', false],
                ],
            ],
            [
                'q' => "What does the `alpha` parameter control in Matplotlib plots like `plt.bar(x, y, alpha=0.6)`?",
                'opts' => [
                    ['The angle of bars in a horizontal bar chart', false],
                    ['The transparency of the chart element — 0 is fully transparent, 1 is fully opaque', true],
                    ['The font size of axis labels', false],
                    ['The statistical significance threshold for the chart', false],
                ],
            ],
            [
                'q' => "A Matplotlib chart has overlapping x-axis tick labels that are impossible to read. Which is the MOST effective fix?",
                'opts' => [
                    ['Reduce the figure DPI to make labels smaller', false],
                    ['Use `plt.xticks(rotation=45, ha=\'right\')` to rotate labels diagonally', true],
                    ['Remove all x-axis labels using plt.xticks([])', false],
                    ['Switch to a pie chart to avoid x-axis labels', false],
                ],
            ],
            [
                'q' => "What does `plt.style.use('seaborn-v0_8-whitegrid')` do?",
                'opts' => [
                    ['Imports Seaborn into the current session', false],
                    ['Applies a pre-defined Matplotlib style with a white background and grid lines, mimicking Seaborn\'s whitegrid style', true],
                    ['Converts all Matplotlib charts to Seaborn charts', false],
                    ['Loads the Seaborn color palette but keeps the Matplotlib default grid', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 15 — Data Visualization (Intermediate).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Intermediate");
    }
}