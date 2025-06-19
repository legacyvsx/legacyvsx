<?php
                // Get the data begins here
                require_once("database.php");
                $conn = connectDB();

                // SQL query to get all rows where date equals the maximum date
                $sql = "SELECT * FROM headlines WHERE date = (SELECT MAX(date) FROM headlines)";

                // Execute the query
                $result = executeQuery($sql, $conn);
                $headlines=array();
                // Check if there are results
                if (mysqli_num_rows($result) > 0) {
                        // Fetch all rows as associative array
                        //$headlines = [];
                        while ($row = mysqli_fetch_assoc($result)) {
                                $headlines[] = $row;
                        }
                }

?>
<style>
* {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
		/* Table Styles */
        .table-container {
            overflow-x: auto;
            margin: 2rem 0;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            background: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        th {
            background: linear-gradient(135deg, #1a1a1a, #333);
            color: white;
            padding: 1.2rem 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: top;
            font-size: 0.9rem;
        }


        .sentiment-positive { color: #28a745; font-weight: 600; }
        .sentiment-negative { color: #dc3545; font-weight: 600; }
        .sentiment-neutral { color: #6c757d; font-weight: 600; }

</style>
<div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Article Title</th>
                            <th>Legacy Sentiment</th>
                            <th>Legacy Emotion</th>
                            <th>X Sentiment</th>
                            <th>X Emotion</th>

                            <th>Analysis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                                foreach($headlines as $headline)
                                {

                        ?>
                        <tr>
                            <td><?php echo date('F j, Y @ g:iA', strtotime($headline['pub_date'])); ?></td>
                            <td><?php echo "<a href=\"" . $headline['url'] . "\">" . $headline['title'] . "</a>"; ?></td>
                            <td class="
                                <?php
                                        if ($headline['a_sent'] < 0.4)
                                        {
                                                echo "sentiment-negative";
                                        }
                                        else if ($headline['a_sent'] > 0.6)
                                        {
                                                echo "sentiment-positive";
                                        }
                                        else
                                        {
                                                echo "sentiment-neutral";
                                        }

                                ?>"><?php echo $headline['a_sent']; ?></td>
                            <td><span><?php echo ucfirst($headline['a_emo']); ?></span></td>
                            <td class="
                                <?php
                                        if ($headline['x_sent'] < 0.4)
                                        {
                                                echo "sentiment-negative";
                                        }
                                        else if ($headline['x_sent'] > 0.6)
                                        {
                                                echo "sentiment-positive";
                                        }
                                        else
                                        {
                                                echo "sentiment-neutral";
                                        }

                                ?>"><?php echo $headline['x_sent']; ?></td>
                            <td><span><?php echo ucfirst($headline['x_emo']); ?></span></td>

                            <td><?php echo $headline['commentary']; ?></td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
