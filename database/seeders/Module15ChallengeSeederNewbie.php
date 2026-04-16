<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module15ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        /*
         * MODULE ORDERING FIX:
         * The map page orders challenges by `id ASC`. If other challenges were
         * seeded before this one, they'd get lower IDs and push Module 15 out of position.
         *
         * Solution: add an `order_index` column to the challenges table and
         * order by that instead. See the migration note at the bottom of this
         * file. For now we also delete any existing challenges for this category
         * and re-insert so IDs are clean.
         */

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 15 — Data Visualization (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Data Visualization',
            'description'           => 'Test your very basic understanding of data visualization — what charts are, what they are used for, and the most common types. No prior programming or statistics experience assumed!',
            'time_limit_seconds'    => 900, // 15 minutes for 50 questions
            'base_xp'               => 500,
            'order_index'           => 15, // Ensure this appears 15th on the map
        ]);

        $this->command->info("Seeding 50 newbie-friendly questions...");

        $qaData = [

            // ── 15.1 INTRODUCTION TO DATA VISUALIZATION ───────────────────
            [
                'q' => 'What is data visualization?',
                'opts' => [
                    ['A method of storing data in a database', false],
                    ['The practice of representing data using charts, graphs, and other visual formats', true],
                    ['A programming technique for cleaning data', false],
                    ['A type of machine learning algorithm', false],
                ],
            ],
            [
                'q' => 'Why do we use data visualization?',
                'opts' => [
                    ['To make data harder to understand', false],
                    ['To replace data with pictures entirely', false],
                    ['To make patterns, trends, and insights in data easier to see and understand', true],
                    ['To slow down the data analysis process', false],
                ],
            ],
            [
                'q' => 'Which of the following is a data visualization tool in Python?',
                'opts' => [
                    ['NumPy', false],
                    ['Matplotlib', true],
                    ['Pandas', false],
                    ['Scikit-learn', false],
                ],
            ],
            [
                'q' => 'What does a chart or graph use to represent data?',
                'opts' => [
                    ['Only text and numbers in a table', false],
                    ['Visual elements such as bars, lines, dots, or slices', true],
                    ['Audio signals and sound waves', false],
                    ['Database queries and SQL commands', false],
                ],
            ],
            [
                'q' => 'Which of the following is NOT a data visualization library in Python?',
                'opts' => [
                    ['Matplotlib', false],
                    ['Seaborn', false],
                    ['Plotly', false],
                    ['Requests', true],
                ],
            ],

            // ── 15.2 MATPLOTLIB: LINE & BAR CHARTS ────────────────────────
            [
                'q' => 'What type of chart is best for showing how a value changes over time?',
                'opts' => [
                    ['Pie chart', false],
                    ['Line chart', true],
                    ['Histogram', false],
                    ['Box plot', false],
                ],
            ],
            [
                'q' => 'What type of chart is best for comparing values across different categories?',
                'opts' => [
                    ['Line chart', false],
                    ['Scatter plot', false],
                    ['Bar chart', true],
                    ['Heatmap', false],
                ],
            ],
            [
                'q' => 'Which Matplotlib function creates a basic line plot?',
                'opts' => [
                    ['plt.bar()', false],
                    ['plt.plot()', true],
                    ['plt.scatter()', false],
                    ['plt.hist()', false],
                ],
            ],
            [
                'q' => 'Which Matplotlib function creates a bar chart?',
                'opts' => [
                    ['plt.plot()', false],
                    ['plt.line()', false],
                    ['plt.bar()', true],
                    ['plt.pie()', false],
                ],
            ],
            [
                'q' => 'What does `plt.show()` do in Matplotlib?',
                'opts' => [
                    ['Saves the chart to a file', false],
                    ['Clears all data from memory', false],
                    ['Displays the chart on screen', true],
                    ['Starts a new empty chart', false],
                ],
            ],
            [
                'q' => 'What does `plt.xlabel("Month")` do?',
                'opts' => [
                    ['Sets the title of the chart to "Month"', false],
                    ['Labels the x-axis with the text "Month"', true],
                    ['Creates a new column called "Month"', false],
                    ['Filters the data by month', false],
                ],
            ],
            [
                'q' => 'What does `plt.title("Sales Report")` do?',
                'opts' => [
                    ['Creates a new DataFrame called "Sales Report"', false],
                    ['Adds a title to the chart', true],
                    ['Saves the chart as "Sales Report.png"', false],
                    ['Prints the text "Sales Report" to the terminal', false],
                ],
            ],

            // ── 15.3 HISTOGRAMS, BOX PLOTS & DISTRIBUTIONS ────────────────
            [
                'q' => 'What does a histogram show?',
                'opts' => [
                    ['The relationship between two variables', false],
                    ['The proportion of each category in a whole', false],
                    ['The frequency distribution of a single numeric variable', true],
                    ['How values change over time', false],
                ],
            ],
            [
                'q' => 'Which Matplotlib function creates a histogram?',
                'opts' => [
                    ['plt.bar()', false],
                    ['plt.hist()', true],
                    ['plt.scatter()', false],
                    ['plt.box()', false],
                ],
            ],
            [
                'q' => 'What is a "bin" in a histogram?',
                'opts' => [
                    ['A type of chart element that shows the legend', false],
                    ['An interval that groups values together, shown as a single bar', true],
                    ['A color applied to the chart background', false],
                    ['A Python variable that stores data', false],
                ],
            ],
            [
                'q' => 'What does a box plot (box-and-whisker plot) display?',
                'opts' => [
                    ['The number of categories in a dataset', false],
                    ['A summary of data distribution: median, quartiles, and potential outliers', true],
                    ['The correlation between two variables', false],
                    ['Only the minimum and maximum values', false],
                ],
            ],
            [
                'q' => 'In a box plot, the line inside the box represents the:',
                'opts' => [
                    ['Mean', false],
                    ['Mode', false],
                    ['Median', true],
                    ['Standard deviation', false],
                ],
            ],

            // ── 15.4 SCATTER PLOTS & CORRELATION ANALYSIS ─────────────────
            [
                'q' => 'What is a scatter plot used for?',
                'opts' => [
                    ['To show parts of a whole using slices', false],
                    ['To display the relationship between two numeric variables', true],
                    ['To compare categories using bars', false],
                    ['To show how a single value changes over time', false],
                ],
            ],
            [
                'q' => 'Which Matplotlib function creates a scatter plot?',
                'opts' => [
                    ['plt.plot()', false],
                    ['plt.bar()', false],
                    ['plt.scatter()', true],
                    ['plt.hist()', false],
                ],
            ],
            [
                'q' => 'In a scatter plot, each dot represents:',
                'opts' => [
                    ['The total sum of all values', false],
                    ['A single data point with an x and y value', true],
                    ['A category label', false],
                    ['The average of all data', false],
                ],
            ],
            [
                'q' => 'If dots in a scatter plot go from bottom-left to top-right, what kind of correlation does this suggest?',
                'opts' => [
                    ['Negative correlation', false],
                    ['No correlation', false],
                    ['Positive correlation', true],
                    ['Perfect correlation of 1.0 always', false],
                ],
            ],

            // ── 15.5 HEATMAPS & CORRELATION MATRICES ──────────────────────
            [
                'q' => 'What does a heatmap use to represent values?',
                'opts' => [
                    ['Bar heights', false],
                    ['Line slopes', false],
                    ['Colors, where darker or lighter shades indicate higher or lower values', true],
                    ['Dot sizes', false],
                ],
            ],
            [
                'q' => 'Which Python library has a built-in `heatmap()` function?',
                'opts' => [
                    ['Matplotlib', false],
                    ['NumPy', false],
                    ['Seaborn', true],
                    ['Pandas', false],
                ],
            ],
            [
                'q' => 'What is a correlation matrix?',
                'opts' => [
                    ['A table that shows how different products are priced', false],
                    ['A table that shows the correlation coefficient between each pair of numeric columns', true],
                    ['A matrix used for multiplying two arrays together', false],
                    ['A type of scatter plot with multiple axes', false],
                ],
            ],
            [
                'q' => 'In a correlation heatmap, a value close to +1 between two features means:',
                'opts' => [
                    ['They have no relationship', false],
                    ['They have a strong negative relationship', false],
                    ['They have a strong positive relationship', true],
                    ['One feature causes the other', false],
                ],
            ],

            // ── 15.6 PAIR PLOTS & FACETGRIDS ──────────────────────────────
            [
                'q' => 'What does a pair plot (also called a scatter matrix) show?',
                'opts' => [
                    ['A single scatter plot between two variables', false],
                    ['Scatter plots for every combination of numeric columns in a dataset at once', true],
                    ['A matrix of correlation values like a heatmap', false],
                    ['A bar chart for every categorical column', false],
                ],
            ],
            [
                'q' => 'Which Seaborn function creates a pair plot?',
                'opts' => [
                    ['sns.heatmap()', false],
                    ['sns.scatterplot()', false],
                    ['sns.pairplot()', true],
                    ['sns.barplot()', false],
                ],
            ],
            [
                'q' => 'What appears along the diagonal of a Seaborn pair plot by default?',
                'opts' => [
                    ['Scatter plots of the variable against itself', false],
                    ['A histogram or KDE plot showing the distribution of each variable', true],
                    ['A blank space', false],
                    ['A correlation value', false],
                ],
            ],

            // ── 15.7 PIE, DONUT & PART-TO-WHOLE CHARTS ────────────────────
            [
                'q' => 'What does a pie chart show?',
                'opts' => [
                    ['How a value changes over time', false],
                    ['The relationship between two numeric variables', false],
                    ['The proportion of each category as a slice of the total', true],
                    ['The distribution of a single variable in bins', false],
                ],
            ],
            [
                'q' => 'Which Matplotlib function creates a pie chart?',
                'opts' => [
                    ['plt.bar()', false],
                    ['plt.pie()', true],
                    ['plt.circle()', false],
                    ['plt.donut()', false],
                ],
            ],
            [
                'q' => 'In a pie chart, all slices together must add up to:',
                'opts' => [
                    ['50%', false],
                    ['More than 100%', false],
                    ['Exactly 100%', true],
                    ['Any total is acceptable', false],
                ],
            ],
            [
                'q' => 'What is a donut chart?',
                'opts' => [
                    ['A pie chart with a hole in the center', true],
                    ['A circular bar chart', false],
                    ['A histogram shaped in a circle', false],
                    ['A type of scatter plot for circular data', false],
                ],
            ],

            // ── 15.8 SUBPLOTS, LAYOUTS & CUSTOMIZATION ────────────────────
            [
                'q' => 'What is a subplot in Matplotlib?',
                'opts' => [
                    ['A type of hidden chart that is not visible to users', false],
                    ['One of multiple charts arranged together within a single figure', true],
                    ['A smaller dataset used for testing', false],
                    ['A zoom feature on an existing chart', false],
                ],
            ],
            [
                'q' => 'Which Matplotlib function is used to create multiple subplots?',
                'opts' => [
                    ['plt.figure()', false],
                    ['plt.subplots()', true],
                    ['plt.grid()', false],
                    ['plt.layout()', false],
                ],
            ],
            [
                'q' => 'What does `plt.legend()` add to a chart?',
                'opts' => [
                    ['A title at the top of the chart', false],
                    ['Labels identifying each line, bar, or series in the chart', true],
                    ['Gridlines in the background', false],
                    ['A color palette to the chart', false],
                ],
            ],
            [
                'q' => 'What does `plt.grid(True)` do?',
                'opts' => [
                    ['Creates a new grid layout with subplots', false],
                    ['Adds gridlines to the background of the chart to make values easier to read', true],
                    ['Turns the chart into a table', false],
                    ['Arranges data into a grid format', false],
                ],
            ],
            [
                'q' => 'What does `plt.savefig("chart.png")` do?',
                'opts' => [
                    ['Displays the chart in the browser', false],
                    ['Saves the current figure to a file named "chart.png"', true],
                    ['Loads a chart from a file named "chart.png"', false],
                    ['Converts the chart to a DataFrame', false],
                ],
            ],

            // ── 15.9 INTERACTIVE CHARTS WITH PLOTLY ───────────────────────
            [
                'q' => 'What makes Plotly charts different from Matplotlib charts?',
                'opts' => [
                    ['Plotly charts are always black and white', false],
                    ['Plotly charts are interactive — users can hover, zoom, and pan', true],
                    ['Plotly can only create pie charts', false],
                    ['Plotly charts cannot be saved as files', false],
                ],
            ],
            [
                'q' => 'Which Python library is used to create interactive data visualizations?',
                'opts' => [
                    ['Seaborn', false],
                    ['Matplotlib', false],
                    ['Plotly', true],
                    ['Pandas', false],
                ],
            ],
            [
                'q' => 'What does "interactive" mean in the context of a chart?',
                'opts' => [
                    ['The chart changes its data automatically every second', false],
                    ['The user can hover over, zoom into, or click on elements of the chart', true],
                    ['The chart connects to the internet to fetch new data', false],
                    ['The chart is printed on paper', false],
                ],
            ],

            // ── 15.10 BEST PRACTICES, COLOR & STORYTELLING ────────────────
            [
                'q' => 'What is the most important goal of a good data visualization?',
                'opts' => [
                    ['To use as many colors as possible', false],
                    ['To make the chart look as complex as possible', false],
                    ['To communicate information clearly and accurately to the audience', true],
                    ['To include every data point in a single chart', false],
                ],
            ],
            [
                'q' => 'Why should you add a title to every chart you create?',
                'opts' => [
                    ['To make the chart file size larger', false],
                    ['To tell the viewer what the chart is about at a glance', true],
                    ['Because Matplotlib requires it or it crashes', false],
                    ['To replace the x-axis label', false],
                ],
            ],
            [
                'q' => 'Which of the following is a BAD practice in data visualization?',
                'opts' => [
                    ['Using a legend when plotting multiple lines', false],
                    ['Adding axis labels to both axes', false],
                    ['Starting a bar chart y-axis at a value other than zero to exaggerate differences', true],
                    ['Choosing a chart type that matches the data', false],
                ],
            ],
            [
                'q' => 'Why is using too many colors in a single chart a bad idea?',
                'opts' => [
                    ['Colors slow down the rendering of the chart', false],
                    ['It can confuse the viewer and make the chart hard to read', true],
                    ['Matplotlib only supports 3 colors at a time', false],
                    ['Colors cannot be used in bar charts', false],
                ],
            ],
            [
                'q' => 'When should you use a line chart instead of a bar chart?',
                'opts' => [
                    ['When comparing categories that have no natural order', false],
                    ['When showing proportions of a whole', false],
                    ['When showing a continuous trend or change over time', true],
                    ['When displaying the distribution of a single variable', false],
                ],
            ],
            [
                'q' => 'What does "data storytelling" mean?',
                'opts' => [
                    ['Writing fiction using data as inspiration', false],
                    ['Using visualizations and narrative to communicate insights from data in a compelling way', true],
                    ['Creating animated movies from datasets', false],
                    ['Turning all charts into tables of numbers', false],
                ],
            ],
            [
                'q' => 'Which chart type is MOST appropriate for showing the distribution of exam scores across 500 students?',
                'opts' => [
                    ['Pie chart', false],
                    ['Histogram', true],
                    ['Line chart', false],
                    ['Scatter plot', false],
                ],
            ],
            [
                'q' => 'What does the color bar (colorbar) on a heatmap represent?',
                'opts' => [
                    ['The categories of data in the chart', false],
                    ['A scale showing which colors correspond to which values', true],
                    ['A legend for the x-axis labels', false],
                    ['The number of rows in the DataFrame', false],
                ],
            ],
            [
                'q' => 'Which of the following is the BEST chart to use when comparing sales of 5 products for the same month?',
                'opts' => [
                    ['Line chart', false],
                    ['Scatter plot', false],
                    ['Bar chart', true],
                    ['Histogram', false],
                ],
            ],
            [
                'q' => 'What does the `hue` parameter in Seaborn plots do?',
                'opts' => [
                    ['Sets the background color of the entire figure', false],
                    ['Colors data points differently based on a categorical variable, adding a third dimension', true],
                    ['Adjusts the brightness of all chart elements', false],
                    ['Applies a gradient color to bars', false],
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

        // $this->command->info("✅ Done! 50 questions seeded for Module 15 — Data Visualization.");
        // $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Newbie");
        // $this->command->newLine();
        // $this->command->comment("NOTE: If Module 15 doesn't appear in the correct position on the map, add an `order_index`");
        // $this->command->comment("column to the `challenges` table and update the map controller query:");
        // $this->command->comment("  ->orderBy('order_index', 'asc')");
        // $this->command->comment("Then set order_index = 15 for this challenge.");
    }
}

/*
 * ─── OPTIONAL MIGRATION (if ordering is still wrong after a fresh seed) ──────
 *
 * If you already have challenges with lower IDs (from the old ChallengeSeeder),
 * add an order_index column so you can control the display order independently:
 *
 *   Schema::table('challenges', function (Blueprint $table) {
 *       $table->integer('order_index')->default(0)->after('base_xp');
 *   });
 *
 * Then in ChallengesController::map(), change:
 *   ->orderBy('id', 'asc')
 * to:
 *   ->orderBy('order_index', 'asc')->orderBy('id', 'asc')
 *
 * And set order_index = 15 on this challenge after seeding:
 *   Challenge::where('title', 'Data Visualization')->update(['order_index' => 15]);
 */