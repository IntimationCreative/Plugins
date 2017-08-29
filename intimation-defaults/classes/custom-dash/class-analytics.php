<?php
/**
 * Dashboard Analytics 
 */
class DashAnalytics  
{
    public $type, $analytics, $reports, $results, $view_id, $dateRange, $buckets, $ordering, $request;

    public function __construct($type = '')
    {
        $this->type = $type;
        $this->view_id = "149114861";
        
        // Load the Google API PHP Client Library.
        require_once IDEF_ABS_FOLDER . '/vendor/autoload.php';
        $this->analytics = $this->initializeAnalytics();
        
        $this->dateRange = $this->date_range();
        $this->buckets = $this->dimensions();
        $this->ordering = $this->ordering();
        $this->request = $this->reportRequest();
        
    }

    public function load_view( $type )
    {
        include IDEF_ABS_FOLDER . '/classes/custom-dash/views/' . $type . '-widget-view.php';
    }

    public function get( $variable )
    {
        return $this->$variable;
    }

    public function users_by_day()
    {
        $this->reports = $this->users_per_day($this->analytics);
        $this->results = $this->printResults($this->reports);
        $this->load_view($this->type);
    }
    
    public function sessions_by_day()
    {
        $this->reports = $this->session_per_day($this->analytics);
        $this->results = $this->printResults($this->reports);
        $this->load_view($this->type);
    }
    
    public function bouncerate_by_day()
    {
        $this->reports = $this->bouncerate($this->analytics);
        $this->results = $this->printResults($this->reports);
        $this->load_view($this->type);
    }
    
    public function sessionDuration_by_day()
    {
        $this->reports = $this->sessionDuration($this->analytics);
        $this->results = $this->printResults($this->reports);
        $this->load_view($this->type);
    }

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

    function date_range()
    {
        // Create the DateRange object.
        $dateRange = new Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate("30daysAgo");
        // $dateRange->setEndDate("today");
        // $dateRange->setStartDate("2017-08-14");
        // $dateRange->setEndDate("2017-08-21");
        // $dateRange->setStartDate("7daysAgo");
        $dateRange->setEndDate("today");
        return $dateRange;
    }

    function dimensions()
    {
        // Create the Dimensions object.
        $buckets = new Google_Service_AnalyticsReporting_Dimension();
        $buckets->setName("ga:nthDay");
        $buckets->setHistogramBuckets(array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29));
        return $buckets;
    }

    function ordering()
    {
        $ordering = new Google_Service_AnalyticsReporting_OrderBy();
        $ordering->setOrderType("HISTOGRAM_BUCKET");
        $ordering->setFieldName("ga:nthDay");
        return $ordering;
    }

    function reportRequest()
    {
        // Create the ReportRequest object.
        $request = new Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($this->view_id);
        $request->setDateRanges($this->dateRange);
        
        $request->setDimensions(array($this->buckets));
        $request->setOrderBys(array($this->ordering));
        return $request;
    }


    /**
     * Queries the Analytics Reporting API V4.
     *
     * @param service An authorized Analytics Reporting API V4 service object.
     * @return The Analytics Reporting API V4 response.
     */
    function users_per_day($analytics) {

        $users = new Google_Service_AnalyticsReporting_Metric();
        $users->setExpression("ga:users");
        $users->setAlias("users");
        $this->request->setMetrics(array($users));

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests( array( $this->request) );
        return $analytics->reports->batchGet( $body );
    }

    function session_per_day($analytics)
    {
        // Create the Metrics object.
        $sessions = new Google_Service_AnalyticsReporting_Metric();
        $sessions->setExpression("ga:sessions");
        $sessions->setAlias("sessions");
        $this->request->setMetrics(array($sessions));

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests( array( $this->request) );
        return $analytics->reports->batchGet( $body );
    }

    function bouncerate($analytics) 
    {
        // Create the Metrics object.
        $bouncerate = new Google_Service_AnalyticsReporting_Metric();
        $bouncerate->setExpression("ga:bouncerate");
        $bouncerate->setAlias("bouncerate");
        $this->request->setMetrics(array($bouncerate));

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests( array( $this->request) );
        return $analytics->reports->batchGet( $body );
    }
    
    function sessionDuration($analytics) 
    {
        // Create the Metrics object.
        $sessionDuration = new Google_Service_AnalyticsReporting_Metric();
        $sessionDuration->setExpression("ga:sessionDuration/100");
        $sessionDuration->setAlias("sessionDuration");
        $this->request->setMetrics(array($sessionDuration));

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests( array( $this->request) );
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

}
