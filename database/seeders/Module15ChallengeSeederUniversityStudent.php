<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module15ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 15 — Data Visualization (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Data Visualization',
            'description'           => 'Apply your understanding of data visualization at a university level — trace basic chart code, choose the right chart for a given scenario, interpret visual outputs, and reason through simple Matplotlib and Seaborn usage patterns.',
            'time_limit_seconds'    => 1200, // 20 minutes
            'base_xp'               => 750,
            'order_index'           => 15,
        ]);

        $this->command->info("Seeding 50 university-level questions...");

        $qaData = [

            // ── 15.1 INTRODUCTION TO DATA VISUALIZATION ───────────────────
            [
                'q' => "What does the following code do?\n\nimport matplotlib.pyplot as plt\nplt.plot([1, 2, 3], [4, 8, 6])\nplt.show()",
                'opts' => [
                    ['Creates a bar chart with 3 bars', false],
                    ['Creates a line plot connecting (1,4), (2,8), and (3,6)', true],
                    ['Prints the list [4, 8, 6] to the terminal', false],
                    ['Raises an error because two lists cannot be passed to plt.plot()', false],
                ],
            ],
            [
                'q' => 'Which statement best describes when to use Seaborn over Matplotlib?',
                'opts' => [
                    ['Seaborn is used only for 3D plots', false],
                    ['Seaborn provides higher-level, statistically-oriented plots with better default aesthetics, built on top of Matplotlib', true],
                    ['Seaborn is faster than Matplotlib for all chart types', false],
                    ['Seaborn only works with CSV files, not DataFrames', false],
                ],
            ],
            [
                'q' => 'A student has daily website visitor counts for 30 days. Which chart type is MOST appropriate?',
                'opts' => [
                    ['Pie chart', false],
                    ['Bar chart', false],
                    ['Line chart', true],
                    ['Heatmap', false],
                ],
            ],
            [
                'q' => 'What is the difference between `plt.figure(figsize=(10, 5))` and the default figure size?',
                'opts' => [
                    ['It creates 10 figures each with height 5', false],
                    ['It sets the figure dimensions to 10 inches wide and 5 inches tall, overriding the default size', true],
                    ['It sets the resolution of the chart to 10x5 pixels', false],
                    ['figsize is ignored by Matplotlib and has no effect', false],
                ],
            ],
            [
                'q' => 'Which import alias is standard for Matplotlib pyplot?',
                'opts' => [
                    ['import matplotlib as ml', false],
                    ['import matplotlib.pyplot as plt', true],
                    ['from matplotlib import chart as plt', false],
                    ['import pyplot from matplotlib', false],
                ],
            ],

            // ── 15.2 MATPLOTLIB: LINE & BAR CHARTS ────────────────────────
            [
                'q' => "What is the output of this code?\n\nimport matplotlib.pyplot as plt\nplt.bar(['A', 'B', 'C'], [10, 25, 15])\nplt.show()\n\nDescribe what the chart looks like.",
                'opts' => [
                    ['A line connecting three points at heights 10, 25, and 15', false],
                    ['Three vertical bars for categories A, B, C with heights 10, 25, and 15 respectively', true],
                    ['A scatter plot with three dots', false],
                    ['A pie chart with three slices', false],
                ],
            ],
            [
                'q' => "What does the `color` parameter do in `plt.plot(x, y, color='red')`?",
                'opts' => [
                    ['Changes the background color of the figure', false],
                    ['Sets the color of the plotted line or markers to red', true],
                    ['Applies a red filter to all axis labels', false],
                    ['Raises an error — color must be passed as a separate function call', false],
                ],
            ],
            [
                'q' => "What does `plt.plot(x, y, linestyle='--')` produce?",
                'opts' => [
                    ['A solid line plot', false],
                    ['A dashed line plot', true],
                    ['A dotted line plot', false],
                    ['A line plot with markers at each point', false],
                ],
            ],
            [
                'q' => "A student runs:\n\nplt.bar(['Math', 'Science', 'English'], [85, 92, 78])\nplt.ylabel('Average Score')\nplt.title('Class Averages')\n\nWhat is missing to make this a complete, properly labeled chart?",
                'opts' => [
                    ['plt.show() to display the chart and plt.xlabel() to label the x-axis', true],
                    ['plt.legend() is required for all bar charts', false],
                    ['plt.grid() must always be called before plt.show()', false],
                    ['Nothing is missing — the chart is complete', false],
                ],
            ],
            [
                'q' => 'What is the purpose of `plt.barh()` compared to `plt.bar()`?',
                'opts' => [
                    ['plt.barh() creates a histogram while plt.bar() creates a bar chart', false],
                    ['plt.barh() creates a horizontal bar chart; plt.bar() creates a vertical bar chart', true],
                    ['They are identical — barh is just an alias for bar', false],
                    ['plt.barh() can only handle numerical categories', false],
                ],
            ],

            // ── 15.3 HISTOGRAMS, BOX PLOTS & DISTRIBUTIONS ────────────────
            [
                'q' => "What does increasing the `bins` parameter in `plt.hist(data, bins=50)` do compared to `bins=10`?",
                'opts' => [
                    ['The histogram becomes smoother with fewer bars', false],
                    ['Each bin covers a narrower range of values, giving a more detailed view of the distribution', true],
                    ['It plots 50 separate charts instead of 1', false],
                    ['The y-axis scale changes from count to percentage', false],
                ],
            ],
            [
                'q' => "Interpret this box plot description: Q1=20, Q3=50, median=35, whiskers extend to 5 and 80. What is the IQR?",
                'opts' => [
                    ['35', false],
                    ['75', false],
                    ['30', true],
                    ['15', false],
                ],
            ],
            [
                'q' => 'In a box plot, which points are typically shown as individual dots outside the whiskers?',
                'opts' => [
                    ['The mean values', false],
                    ['Outliers — data points falling beyond 1.5 × IQR from Q1 or Q3', true],
                    ['Duplicate values', false],
                    ['The mode of the dataset', false],
                ],
            ],
            [
                'q' => "Which Seaborn function creates a box plot?\n\nsns._____(x='category', y='value', data=df)",
                'opts' => [
                    ['sns.hist()', false],
                    ['sns.distplot()', false],
                    ['sns.boxplot()', true],
                    ['sns.scatterplot()', false],
                ],
            ],
            [
                'q' => 'A histogram of exam scores is right-skewed (long tail on the right). What does this tell you about the scores?',
                'opts' => [
                    ['Most students scored very high with a few very low scores', false],
                    ['The scores are evenly distributed', false],
                    ['Most students scored lower and fewer students achieved very high scores', true],
                    ['The histogram was created with too many bins', false],
                ],
            ],

            // ── 15.4 SCATTER PLOTS & CORRELATION ANALYSIS ─────────────────
            [
                'q' => "What does the following Seaborn code produce?\n\nsns.scatterplot(x='height', y='weight', data=df, hue='gender')",
                'opts' => [
                    ['A single scatter plot where all dots are the same color', false],
                    ['A scatter plot of height vs. weight where dots are colored by gender', true],
                    ['A bar chart comparing height and weight by gender', false],
                    ['A correlation heatmap of height, weight, and gender', false],
                ],
            ],
            [
                'q' => "A scatter plot of study hours (x) vs. exam score (y) shows dots trending upward from left to right. Which statement is correct?",
                'opts' => [
                    ['More study hours causes higher exam scores', false],
                    ['There is a positive correlation between study hours and exam score', true],
                    ['The correlation coefficient must be exactly 1.0', false],
                    ['There is a negative correlation because the dots are spread out', false],
                ],
            ],
            [
                'q' => 'What does `plt.scatter(x, y, s=100)` do differently from `plt.scatter(x, y)`?',
                'opts' => [
                    ['s=100 sets the number of points to plot to 100', false],
                    ['s=100 sets the size of each marker/dot to 100', true],
                    ['s=100 sets the speed of rendering to 100ms', false],
                    ['s=100 filters the data to show only the top 100 values', false],
                ],
            ],
            [
                'q' => 'In a scatter plot, if all points form a nearly perfect diagonal line from top-left to bottom-right, what does this suggest?',
                'opts' => [
                    ['A strong positive correlation', false],
                    ['No correlation', false],
                    ['A strong negative correlation', true],
                    ['A quadratic (curved) relationship', false],
                ],
            ],

            // ── 15.5 HEATMAPS & CORRELATION MATRICES ──────────────────────
            [
                'q' => "What does the following code produce?\n\nimport seaborn as sns\ncorr = df.corr()\nsns.heatmap(corr, annot=True)",
                'opts' => [
                    ['A scatter plot matrix of all variables', false],
                    ['A color-coded heatmap of the correlation matrix with numerical values in each cell', true],
                    ['A bar chart of correlation values for each feature', false],
                    ['A line chart of feature correlations over time', false],
                ],
            ],
            [
                'q' => 'In a Seaborn heatmap, what does `annot=True` do?',
                'opts' => [
                    ['Adds annotations (the numeric values) inside each cell of the heatmap', true],
                    ['Annotates the chart title automatically', false],
                    ['Enables animated transitions between colors', false],
                    ['Adds axis tick labels on both sides', false],
                ],
            ],
            [
                'q' => "A correlation matrix shows the value -0.91 between `temperature` and `hot_chocolate_sales`. What does this mean?",
                'opts' => [
                    ['Temperature and hot chocolate sales have no relationship', false],
                    ['As temperature increases, hot chocolate sales tend to strongly decrease', true],
                    ['Hot chocolate causes temperature to drop', false],
                    ['The value -0.91 is invalid — correlations must be between 0 and 1', false],
                ],
            ],
            [
                'q' => 'What does `cmap="coolwarm"` do in a Seaborn heatmap call?',
                'opts' => [
                    ['Sets the color palette so negative values appear cool (blue) and positive values appear warm (red)', true],
                    ['Cools down the rendering speed of the chart', false],
                    ['Resets the heatmap to black and white', false],
                    ['Applies a smoothing filter to the cell values', false],
                ],
            ],

            // ── 15.6 PAIR PLOTS & FACETGRIDS ──────────────────────────────
            [
                'q' => "What does `sns.pairplot(df, hue='species')` do?",
                'opts' => [
                    ['Creates a single scatter plot colored by species', false],
                    ['Creates a grid of scatter plots for all column pairs, with points colored by species', true],
                    ['Creates a heatmap grouped by species', false],
                    ['Draws one histogram per species in separate windows', false],
                ],
            ],
            [
                'q' => 'What is the purpose of a FacetGrid in Seaborn?',
                'opts' => [
                    ['To create a 3D scatter plot with multiple faces', false],
                    ['To create a grid of subplots, each showing a subset of data based on one or more categorical variables', true],
                    ['To merge multiple DataFrames for plotting', false],
                    ['To generate interactive tooltips on hover', false],
                ],
            ],
            [
                'q' => 'In a pair plot, if the off-diagonal scatter plots all show tight linear patterns, what does this suggest about the dataset?',
                'opts' => [
                    ['The dataset has many missing values', false],
                    ['Most numeric features are strongly correlated with each other', true],
                    ['The dataset has no categorical variables', false],
                    ['The pair plot was created with incorrect parameters', false],
                ],
            ],

            // ── 15.7 PIE, DONUT & PART-TO-WHOLE CHARTS ────────────────────
            [
                'q' => "What is wrong with the following pie chart data?\n\nlabels = ['A', 'B', 'C']\nsizes  = [40, 35, 40]\nplt.pie(sizes, labels=labels)",
                'opts' => [
                    ['Pie charts cannot have 3 slices', false],
                    ['The sizes add up to 115, not 100, making the proportions misleading', true],
                    ['Labels cannot be strings in plt.pie()', false],
                    ['Nothing is wrong — Matplotlib normalizes the values automatically (though that may still mislead)', false],
                ],
            ],
            [
                'q' => 'How do you create a donut chart using Matplotlib?',
                'opts' => [
                    ['Use plt.donut() with a radius parameter', false],
                    ['Use plt.pie() with a `wedgeprops` dict setting the width less than the radius, leaving a center hole', true],
                    ['Use plt.bar() with circular styling', false],
                    ['Donut charts require Plotly — Matplotlib cannot create them', false],
                ],
            ],
            [
                'q' => 'What does the `explode` parameter in `plt.pie()` do?',
                'opts' => [
                    ['Removes a slice from the pie chart entirely', false],
                    ['Separates (offsets) a specific slice outward from the center for emphasis', true],
                    ['Explodes the chart into multiple subplots', false],
                    ['Raises an explosion error if slices do not sum to 100', false],
                ],
            ],
            [
                'q' => 'When is a pie chart a POOR choice for data visualization?',
                'opts' => [
                    ['When there are exactly 3 categories', false],
                    ['When the data adds up to 100%', false],
                    ['When there are many categories with similar proportions, making slices hard to distinguish', true],
                    ['When the values are whole numbers', false],
                ],
            ],

            // ── 15.8 SUBPLOTS, LAYOUTS & CUSTOMIZATION ────────────────────
            [
                'q' => "What does `fig, axes = plt.subplots(2, 3)` create?",
                'opts' => [
                    ['2 figures each with 3 axes', false],
                    ['A single figure with a 2-row by 3-column grid of subplots (6 total)', true],
                    ['6 separate windows each showing a different chart', false],
                    ['A figure with 2 columns and 3 rows (6 total)', false],
                ],
            ],
            [
                'q' => "How do you plot on the second subplot (index 1) in a row of 3?\n\nfig, axes = plt.subplots(1, 3)",
                'opts' => [
                    ['plt.plot(x, y)', false],
                    ['axes[1].plot(x, y)', true],
                    ['axes.plot(1, x, y)', false],
                    ['fig[1].plot(x, y)', false],
                ],
            ],
            [
                'q' => "What does `plt.tight_layout()` do?",
                'opts' => [
                    ['Compresses all data to fit in one chart', false],
                    ['Automatically adjusts subplot spacing to prevent overlapping labels and titles', true],
                    ['Forces all subplots to share the same y-axis scale', false],
                    ['Locks the figure size so it cannot be resized', false],
                ],
            ],
            [
                'q' => "What does the following code add to a chart?\n\nplt.axhline(y=50, color='red', linestyle='--')",
                'opts' => [
                    ['A vertical red dashed line at x=50', false],
                    ['A horizontal red dashed line across the chart at y=50', true],
                    ['A red dot at the coordinate (50, 50)', false],
                    ['A red shaded region between y=0 and y=50', false],
                ],
            ],

            // ── 15.9 INTERACTIVE CHARTS WITH PLOTLY ───────────────────────
            [
                'q' => "What does the following Plotly Express code create?\n\nimport plotly.express as px\nfig = px.line(df, x='date', y='sales')\nfig.show()",
                'opts' => [
                    ['A static line chart that cannot be zoomed', false],
                    ['An interactive line chart of sales over date where users can hover, zoom, and pan', true],
                    ['A bar chart of sales by date', false],
                    ['A PDF export of the sales line chart', false],
                ],
            ],
            [
                'q' => 'What is Plotly Express (`plotly.express`) compared to `plotly.graph_objects`?',
                'opts' => [
                    ['Plotly Express is slower but more customizable than graph_objects', false],
                    ['Plotly Express provides a simpler, high-level API for common chart types; graph_objects is lower-level and more customizable', true],
                    ['They are identical libraries with different names', false],
                    ['graph_objects is deprecated and should no longer be used', false],
                ],
            ],
            [
                'q' => 'Which Plotly Express function creates an interactive scatter plot?',
                'opts' => [
                    ['px.bar()', false],
                    ['px.scatter()', true],
                    ['px.plot()', false],
                    ['px.dot()', false],
                ],
            ],
            [
                'q' => 'What does the `hover_data` parameter in Plotly Express charts do?',
                'opts' => [
                    ['Adds extra columns of information displayed in the tooltip when hovering over a data point', true],
                    ['Enables zoom on mouse hover', false],
                    ['Sets which data is shown on the x-axis', false],
                    ['Filters data to show only hovered categories', false],
                ],
            ],

            // ── 15.10 BEST PRACTICES, COLOR & STORYTELLING ────────────────
            [
                'q' => 'A data scientist presents a bar chart where the y-axis starts at 95 instead of 0 to show differences between values of 96, 97, and 98. What is the problem?',
                'opts' => [
                    ['Bar charts must always display integers', false],
                    ['The truncated y-axis exaggerates small differences, potentially misleading the audience', true],
                    ['The chart should use a pie chart instead', false],
                    ['The y-axis should start at 100 for percentage data', false],
                ],
            ],
            [
                'q' => 'What is the purpose of a color palette in data visualization?',
                'opts' => [
                    ['To make the chart look artistic', false],
                    ['To consistently and meaningfully encode categorical or sequential information through color', true],
                    ['To ensure all charts use exactly the same blue color', false],
                    ['To replace axis labels with colored text', false],
                ],
            ],
            [
                'q' => 'Which Seaborn function sets the overall style of charts (e.g., background, gridlines)?',
                'opts' => [
                    ['sns.palette()', false],
                    ['sns.set_style()', true],
                    ['sns.theme()', false],
                    ['sns.configure()', false],
                ],
            ],
            [
                'q' => 'You have monthly revenue data for 3 product lines over 24 months. Which chart is MOST appropriate?',
                'opts' => [
                    ['Three separate pie charts, one per product line', false],
                    ['A multi-line chart with one line per product line, plotted over 24 months', true],
                    ['A heatmap of months vs. product lines', false],
                    ['A histogram of revenue values', false],
                ],
            ],
            [
                'q' => "What does `sns.set_palette('colorblind')` do?",
                'opts' => [
                    ['Makes the chart invisible to colorblind users', false],
                    ['Applies a color palette specifically designed to be distinguishable for colorblind viewers', true],
                    ['Converts the chart to grayscale', false],
                    ['Raises an error — colorblind is not a valid palette name', false],
                ],
            ],
            [
                'q' => 'What is the "data-ink ratio" concept introduced by Edward Tufte?',
                'opts' => [
                    ['The ratio of the number of data points to the number of color options used', false],
                    ['The principle that the proportion of ink used to represent actual data should be maximized, while chart clutter (decorations, gridlines) should be minimized', true],
                    ['A formula for calculating the optimal number of bins in a histogram', false],
                    ['The ratio of chart height to chart width for best readability', false],
                ],
            ],
            [
                'q' => "A student creates a scatter plot and adds a trend line. Which Seaborn function produces a scatter plot WITH a regression line built in?",
                'opts' => [
                    ['sns.scatterplot()', false],
                    ['sns.regplot()', true],
                    ['sns.lmplot()', false],
                    ['Both sns.regplot() and sns.lmplot() — they both add regression lines to scatter plots', false],
                ],
            ],
            [
                'q' => 'Which of the following chart types is BEST suited for displaying the relationship between three numeric variables simultaneously?',
                'opts' => [
                    ['Pie chart', false],
                    ['Bar chart with error bars', false],
                    ['Bubble chart (scatter plot where dot size encodes a third variable)', true],
                    ['Histogram with 3 bins', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 15 — Data Visualization (University Student).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: University Student");
    }
}