<?php

echo "TWO!";

// Load the Google API PHP Client Library.
require_once IDEF_ABS_FOLDER . '/vendor/autoload.php';

$analytics = initializeAnalytics();
$response = getReport($analytics);



/**
 * Initializes an Analytics Reporting API V4 service object.
 *
 * @return An authorized Analytics Reporting API V4 service object.
 */
function initializeAnalytics()
{
	// Use the developers console and download your service account
	// credentials in JSON format. Place them in this directory or
	// change the key file location if necessary.
	$KEY_FILE_LOCATION = IDEF_ABS_FOLDER . '/service-account-credentials.json';

	// Create and configure a new client object.
	$client = new Google_Client();
	$client->setApplicationName("Hello Analytics Reporting");
	$client->setAuthConfig($KEY_FILE_LOCATION);
	$client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
	$analytics = new Google_Service_AnalyticsReporting($client);

	return $analytics;
}


/**
 * Queries the Analytics Reporting API V4.
 *
 * @param service An authorized Analytics Reporting API V4 service object.
 * @return The Analytics Reporting API V4 response.
 */
function getReport($analytics) {

	// Replace with your view ID, for example XXXX.
	$VIEW_ID = "149114861";

	// Create the DateRange object.
	$dateRange = new Google_Service_AnalyticsReporting_DateRange();
	$dateRange->setStartDate("30daysAgo");
	// $dateRange->setEndDate("today");
	// $dateRange->setStartDate("2017-08-14");
	// $dateRange->setEndDate("2017-08-21");
	// $dateRange->setStartDate("7daysAgo");
	$dateRange->setEndDate("today");

	// Create the Metrics object.
	// $sessions = new Google_Service_AnalyticsReporting_Metric();
	// $sessions->setExpression("ga:sessions");
	// $sessions->setAlias("sessions");

	$users = new Google_Service_AnalyticsReporting_Metric();
	$users->setExpression("ga:users");
	$users->setAlias("users");

	// Create the Dimensions object.
	$buckets = new Google_Service_AnalyticsReporting_Dimension();
	$buckets->setName("ga:nthDay");
	$buckets->setHistogramBuckets(array(0,1,2,3,4,5,6));

	// Create the Ordering.
	$ordering = new Google_Service_AnalyticsReporting_OrderBy();
	$ordering->setOrderType("HISTOGRAM_BUCKET");
	$ordering->setFieldName("ga:nthDay");

	
	// $bounce = new Google_Service_AnalyticsReporting_Metric();
	// $bounce->setExpression("ga:bounceRate");
	// $bounce->setAlias("Bounce Rate");
	
	// $sessionDuration = new Google_Service_AnalyticsReporting_Metric();
	// $sessionDuration->setExpression("ga:sessionDuration/100");
	// $sessionDuration->setAlias("sessionDuration");

	// Create the ReportRequest object.
	$request = new Google_Service_AnalyticsReporting_ReportRequest();
	$request->setViewId($VIEW_ID);
	$request->setDateRanges($dateRange);
	// $request->setMetrics(array($users, $sessions, $bounce, $sessionDuration));
	$request->setMetrics(array($users));
	$request->setDimensions(array($buckets));
	$request->setOrderBys(array($ordering));

	$body = new Google_Service_AnalyticsReporting_GetReportsRequest();
	$body->setReportRequests( array( $request) );
	return $analytics->reports->batchGet( $body );
}


/**
 * Parses and prints the Analytics Reporting API V4 response.
 *
 * @param An Analytics Reporting API V4 response.
 */
function printResults($reports) {
	$finalValues = array();
	
	for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) {
		$report = $reports[ $reportIndex ];
		$header = $report->getColumnHeader();
		$dimensionHeaders = $header->getDimensions();
		$metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
		$rows = $report->getData()->getRows();
		
		for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
			$row = $rows[ $rowIndex ];
			$dimensions = $row->getDimensions();
			$metrics = $row->getMetrics();
			for ($j = 0; $j < count($metrics); $j++) {
				$values = $metrics[$j]->getValues();
				
				for ($k = 0; $k < count($values); $k++) {
					$finalValues[] = $values[$k]; 
				}
			}
		}
	}
	return json_encode($finalValues);
}

?>

<div class="welcome-panel-column welcome-panel-last">
	<canvas id="myChart" width="700" height="700" style="display: block; width: 700px; height: 700px; margin-bottom: 2rem;"></canvas>
</div>

<div class="welcome-panel-column-container">
    <!-- Google Analytics Reporting API v4 Implementation -->
    <style>
        #myChart {
            max-width: 700px;
            max-height: 700px;
        }
    </style>
        
    <script>
        var values = <?php print_r( printResults($response) ); ?>;
        var timeFormat = "MMM DD";
            
        function newDateString(days) {
            return moment().subtract(days, 'd');
        }
        var config = {
            type: 'line',
            data: {
                labels: [ // Date Objects
                    newDateString(7),
                    newDateString(6),
                    newDateString(5),
                    newDateString(4),
                    newDateString(3),
                    newDateString(2),
                    newDateString(1)
                ],
                datasets: [{
                    label: "Users By Day",
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 0.2)',
                    fill: false,
                    // data: [ 8,11,6,7,6,1,4 ],
                    data: values,
                }]
            },
            options: {
                title:{
                    text: "Chart.js Time Scale"
                },
                scales: {
                    xAxes: [{
                        type: "time",
                        time: {
                            format: timeFormat,
                            round: 'day',
                            tooltipFormat: 'll'
                        },
                        scaleLabel: {
                            display: false,
                            labelString: 'Date'
                        }
                    }, ],
                    yAxes: [{
                        scaleLabel: {
                            display: false,
                            labelString: 'value'
                        }
                    }]
                },
            }
        };
        window.onload = function() {
            var ctx = document.getElementById("myChart").getContext("2d");
            window.myLine = new Chart(ctx, config);
        };
    </script>
</div>